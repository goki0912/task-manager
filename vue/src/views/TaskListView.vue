<template>
  <div class="min-h-screen bg-gray-100 py-10 px-4">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
      <h1 class="text-2xl font-bold mb-4">タスク一覧</h1>

      <div class="flex items-center justify-between mb-6">
        <button
          @click="logout"
          class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition"
        >
          ログアウト
        </button>

        <!-- 🔗 LINE連携表示 -->
        <div v-if="user" class="text-sm">
          <template v-if="user.line_user_id">
            ✅ LINE連携済み
          </template>
          <template v-else>
            <button
              @click="connectWithLINE"
              class="ml-4 text-blue-600 hover:underline"
            >
              LINE連携
            </button>
          </template>
        </div>
      </div>

      <!-- タスク作成フォーム -->
      <div ref="formRef">
        <TaskForm
          :task="editingTask"
          @created="fetchTasks"
          @cleared="editingTask = null"
        />
      </div>
      <!-- タスクがない場合 -->
      <div v-if="tasks.length === 0" class="text-gray-500 mt-6">タスクがありません</div>

      <!-- タスクリスト -->
      <ul class="space-y-4 mt-6">
        <li
          v-for="task in tasks"
          :key="task.id"
          class="p-4 border rounded bg-gray-50 shadow-sm flex items-start gap-4"
        >
          <!-- ✅ チェックボックス -->
          <input
            type="checkbox"
            :checked="task.is_done"
            @change="toggleDone(task)"
            class="mt-1"
          />

          <!-- タスク本文 -->
          <div :class="{ 'opacity-50 line-through': task.is_done }">
            <strong class="block text-lg">{{ task.title }}</strong>
            <p class="text-gray-700">{{ task.description }}</p>
            <p class="text-sm text-gray-600 mt-1">📅 期限: {{ formatDate(task.due_date) }}</p>

            <div v-if="task.remind_before_minutes" class="text-sm text-gray-600 mt-1">
              🔔 通知: {{ task.remind_before_minutes }}分前<br />
              🔔 通知予定: {{ formatRemindTime(task.due_date, task.remind_before_minutes) }}
            </div>

            <div v-if="task.assigned_users?.length" class="text-sm text-gray-600 mt-1">
              👥 アサイン: {{ task.assigned_users.map((u) => u.name).join(', ') }}
            </div>

            <!-- 操作ボタン -->
            <div class="mt-2">
              <button
                @click="editTask(task)"
                class="text-blue-500 hover:underline text-sm"
              >
                ✏️ 編集
              </button>
              <button
                @click="deleteTask(task.id)"
                class="text-red-500 hover:underline text-sm ml-4"
              >
                🗑 削除
              </button>
            </div>
          </div>
        </li>

      </ul>

      <!-- エラー表示 -->
      <p v-if="error" class="mt-6 text-red-600 text-sm">{{ error }}</p>
    </div>
  </div>
</template>


<script setup>
import TaskForm from "@/components/TaskForm.vue";
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/index.js'

const tasks = ref([])
const error = ref(null)
const user = ref(null)
const editingTask = ref(null) // ← 編集中のタスク
const formRef = ref(null) // ← フォームを指すDOM参照


const scrollToForm = () => {
  formRef.value?.scrollIntoView({ behavior: 'smooth' })
}

const editTask = (task) => {
  editingTask.value = task
  scrollToForm()
}

const router = useRouter()

// 🔽 ユーザー情報取得
const fetchUser = async () => {
  try {
    const res = await api.getCurrentUser()
    user.value = res.data.data
    console.log("##########")
    console.log(user.value)
  } catch (e) {
    error.value = 'ユーザー情報取得に失敗しました'
  }
}

const fetchTasks = async () => {
  try {
    const res = await api.getTasks()
    tasks.value = res.data.data
  } catch (e) {
    error.value = 'タスク取得に失敗しました'
  }
}

const deleteTask = async (taskId) => {
  if (!confirm('本当にこのタスクを削除しますか？')) return
  try {
    await api.deleteTask(taskId)
    fetchTasks()
  } catch {
    error.value = 'タスク削除に失敗しました'
  }
}

const toggleDone = async (task) => {
  try {
    await api.toggleTaskDone(task.id)
    task.is_done = !task.is_done
  } catch {
    error.value = '完了状態の更新に失敗しました'
  }
}

const logout = async () => {
  try {
    await api.logout()
    localStorage.removeItem('token')
    await router.push('/login')
  } catch (e) {
    error.value = 'ログアウトに失敗しました'
  }
}

const connectWithLINE = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await api.getLineRedirectUrl(token)
    window.location.href = response.data.data.url
  } catch (err) {
    console.error('LINE連携失敗', err)
  }
}

const formatDate = (isoString) => {
  return isoString.slice(0, 16).replace('T', ' ')
}

const formatRemindTime = (dueDate, minutesBefore) => {
  const ms = new Date(dueDate).getTime() - minutesBefore * 60_000
  return new Date(ms).toISOString().slice(0, 16).replace('T', ' ')
}

onMounted(() => {
  fetchTasks()
  fetchUser()
})
</script>
