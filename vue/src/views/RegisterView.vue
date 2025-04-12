<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/index.js'

const name = ref('')
const email = ref('')
const password = ref('')
const error = ref(null)
const router = useRouter()

const register = async () => {
  try {
    const res = await api.register(name.value, email.value, password.value)
    const token = res.data.data.token
    localStorage.setItem('token', token)
    api.setToken(token)
    await router.push('/tasks')
  } catch {
    error.value = '登録に失敗しました'
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white p-8 rounded shadow">
      <h1 class="text-2xl font-bold mb-6 text-center">ユーザー登録</h1>

      <form @submit.prevent="register" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">name</label>
          <input
            v-model="name"
            type="text"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            v-model="email"
            type="email"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">password</label>
          <input
            v-model="password"
            type="password"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition"
        >
          登録
        </button>
      </form>

      <p v-if="error" class="mt-4 text-red-600 text-sm text-center">{{ error }}</p>

      <p class="mt-6 text-sm text-center text-gray-600">
        すでにアカウントをお持ちの方は
        <router-link to="/login" class="text-blue-600 hover:underline">
          ログインはこちら
        </router-link>
      </p>
    </div>
  </div>
</template>
