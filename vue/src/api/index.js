import axios from 'axios'

// axiosインスタンス作成
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  withCredentials: false,
  // 消すかも
  // headers: {
  //   Accept: 'application/json', // 👈 これ追加！
  // },
})

// 認証トークン設定用
const setToken = (token) => {
  api.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

// 認証系
const login = (email, password) => {
  return api.post('/login', { email, password })
}

const register = (name, email, password) => {
  return api.post('/register', { name, email, password })
}

const logout = () => {
  return api.post('/logout')
}

// タスク操作
const getTasks = () => api.get('/tasks')
const deleteTask = (id) => api.delete(`/tasks/${id}`)

const createTask = (data) => api.post('/tasks', data)
const updateTask = (id, data) => api.put(`/tasks/${id}`, data)
const toggleTaskDone = (id) => api.patch(`/tasks/${id}/toggle`)

const assignTask = (taskId, userIds) => api.post(`/tasks/${taskId}/assign`, { user_ids: userIds })
const getLineRedirectUrl = (token) => {
  return api.get(`/line/login?token=${token}`)
}
const getCurrentUser = () => api.get('/me')

export default {
  setToken,
  login,
  register,
  logout,
  getTasks,
  deleteTask,
  createTask,
  updateTask,
  toggleTaskDone,
  assignTask,
  getLineRedirectUrl,
  getCurrentUser,
  instance: api, // 直接アクセスしたい場合用
}
