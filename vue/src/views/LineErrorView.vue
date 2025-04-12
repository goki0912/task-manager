<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded shadow text-center max-w-md">
      <h1 class="text-2xl font-bold text-red-600 mb-4">LINE連携に失敗しました</h1>
      <p class="text-gray-700 mb-6">
        認証トークンが無効または期限切れです。<br />
        再度ログインし、LINE連携をやり直してください。
      </p>
      <button
        @click="goBack"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"
      >
        タスク一覧へ戻る
      </button>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
const router = useRouter()

const goBack = () => {
  router.push('/tasks')
}
</script>
