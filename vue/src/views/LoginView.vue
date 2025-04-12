<template>
  <div class="login min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md bg-white p-8 rounded shadow">
      <h1 class="text-2xl font-bold mb-6 text-center">ログイン</h1>
      <form @submit.prevent="login" class="space-y-4">
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
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
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
          ログイン
        </button>
      </form>

      <p v-if="error" class="mt-4 text-red-600 text-sm text-center">{{ error }}</p>

      <p class="mt-6 text-sm text-center text-gray-600">
        アカウントをお持ちでない方は
        <router-link to="/register" class="text-blue-600 hover:underline">
          こちらから新規登録
        </router-link>
      </p>
    </div>
  </div>
</template>


<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import api from '@/api/index.js'

const email = ref('')
const password = ref('')
const error = ref(null)
const router = useRouter()

const login = async () => {
  try {
    const response = await api.login(email.value, password.value)
    const token = response.data.data.token
    localStorage.setItem('token', token)
    api.setToken(token)
    await router.push('/tasks')
  } catch (err) {
    error.value = 'ログインに失敗しました'
  }
}
</script>
