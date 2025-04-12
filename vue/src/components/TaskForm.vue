<template>
  <form @submit.prevent="createOrUpdate" class="bg-white p-6 rounded shadow space-y-4">
    <h2 class="text-xl font-bold mb-4">
      {{ taskId ? '✏️ タスク編集' : '📝 タスク作成' }}
    </h2>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">タイトル</label>
      <input
        v-model="title"
        placeholder="タスクのタイトル"
        required
        class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">説明</label>
      <input
        v-model="description"
        placeholder="説明"
        class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">期限</label>
      <input
        type="datetime-local"
        v-model="dueDate"
        required
        class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">🔔 リマインド時間（分前）（LINE連携時のみ）</label>
      <input
        type="number"
        v-model="remindBefore"
        min="1"
        placeholder="例: 30"
        class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">👥 アサイン先ユーザー</label>
      <select
        v-model="selectedUserIds"
        multiple
        class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option v-for="user in users" :value="user.id" :key="user.id">
          {{ user.name }}
        </option>
      </select>
    </div>

    <button
      type="submit"
      class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition"
    >
      {{ taskId ? '更新する' : '作成する' }}
    </button>

    <p v-if="error" class="text-red-600 text-sm mt-2">{{ error }}</p>
  </form>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '../api'

const emit = defineEmits(['created', 'cleared'])
const props = defineProps({ task: Object })

const title = ref('')
const description = ref('')
const dueDate = ref('')
const remindBefore = ref(30)
const selectedUserIds = ref([])
const users = ref([])
const error = ref(null)
const taskId = ref(null)

const fetchUsers = async () => {
  try {
    const res = await api.instance.get('/users')
    users.value = res.data.data
  } catch (e) {
    error.value = 'ユーザー取得に失敗しました'
  }
}

// 編集時に task を反映
watch(() => props.task, (newTask) => {
  if (newTask) {
    title.value = newTask.title
    description.value = newTask.description
    dueDate.value = newTask.due_date?.slice(0, 16)
    remindBefore.value = newTask.remind_before_minutes ?? 30
    selectedUserIds.value = newTask.assigned_users?.map(u => u.id) || []
    taskId.value = newTask.id
  } else {
    resetForm()
  }
})

const createOrUpdate = async () => {
  try {
    if (taskId.value) {
      await api.updateTask(taskId.value, {
        title: title.value,
        description: description.value,
        due_date: dueDate.value,
        remind_before_minutes: remindBefore.value,
      })
      await api.assignTask(taskId.value, selectedUserIds.value)
    } else {
      const res = await api.createTask({
        title: title.value,
        description: description.value,
        due_date: dueDate.value,
        remind_before_minutes: remindBefore.value,
      })

      const id = res.data.data.id
      if (selectedUserIds.value.length > 0) {
        await api.assignTask(id, selectedUserIds.value)
      }
    }

    emit('created')  // 再取得
    emit('cleared')  // 編集終了
    resetForm()
  } catch {
    error.value = 'タスク保存に失敗しました'
  }
}

const resetForm = () => {
  title.value = ''
  description.value = ''
  dueDate.value = ''
  remindBefore.value = 30
  selectedUserIds.value = []
  taskId.value = null
}

onMounted(fetchUsers)
</script>
