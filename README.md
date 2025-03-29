# 📝 簡易タスク管理アプリケーション

Laravel12 + Vue3 によるタスク管理アプリケーションです。  
ユーザー登録・ログイン後、自身のタスクを作成・編集・削除でき、他ユーザーをタスクにアサイン可能です。  
LINE通知によるリマインダー機能付き。

---

## 🚀 機能一覧

- ユーザー認証（登録 / ログイン / ログアウト）
- タスクCRUD（title / description / due_date / remind_before_minutes）
- 他ユーザーへのタスクアサイン
- タスク期限のLINE通知（通知時刻カスタマイズ可）
- 認可ポリシーによるアクセス制限（他人のタスクは見られない）
- フロントエンド：Vue3（最低限の構成）

---

## 🧪 テスト

- Pest によるFeatureテスト・認可ポリシーテスト・リマインド通知ジョブテストなどを完備
- テスト用DB（`task_manager_testing`）を使ってデータ破壊を防止

---

## 📦 セットアップ手順（Docker使用）

```bash
cd task-manager

# ビルド＆起動
docker compose up -d --build

# Laravel初期化
docker exec app php artisan migrate
docker exec app php artisan db:seed

# スケジューラーとキューを起動（別ターミナルで）
docker exec app php artisan schedule:work
docker exec app php artisan queue:work






curl -X POST http://localhost:8000/api/register \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{
"name": "Test User",
"email": "test@example.com",
"password": "password"
}'

curl -X POST http://localhost:8000/api/login \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{
"email": "test@example.com",
"password": "password"
}'


curl -X POST http://localhost:8000/api/logout \
-H "Accept: application/json" \
-H "Authorization: Bearer YOUR_TOKEN_HERE"


curl -X POST http://localhost:8000/api/tasks/1/assign \
-H "Authorization: Bearer YOUR_TOKEN" \
-H "Content-Type: application/json" \
-d '{"user_ids": [2, 3]}'

php artisan queue:workとphp artisan schedule:workを動かす
```

---

## 🌐 アクセスURL

| 種別 | URL |
|------|-----|
| フロントエンド(Vue) | http://localhost:5173 |
| バックエンド(API) | http://localhost:8000/api |

---

## 🔐 認証＆認可

- Sanctum を使用した API トークン認証（Bearer）
- 各ユーザーは自分のタスクのみ閲覧・編集・削除可能
- 認可ポリシー（`TaskPolicy`）により保護

---

## 📮 エンドポイント仕様（抜粋）

### 認証系

| メソッド | パス | 概要 |
|---------|------|------|
| POST | `/api/register` | 新規登録 |
| POST | `/api/login` | ログイン |
| POST | `/api/logout` | ログアウト |

### タスク系

| メソッド | パス | 概要 |
|---------|------|------|
| GET | `/api/tasks` | 自分のタスク一覧 |
| GET | `/api/tasks/{id}` | タスク詳細 |
| POST | `/api/tasks` | タスク作成 |
| PUT | `/api/tasks/{id}` | タスク更新 |
| DELETE | `/api/tasks/{id}` | タスク削除 |
| POST | `/api/tasks/{id}/assign` | ユーザーをアサイン |

### その他

| メソッド | パス | 概要 |
|---------|------|------|
| GET | `/api/users` | ユーザー一覧（アサイン候補） |
| GET | `/api/me` | 自分のユーザー情報 |
| GET | `/api/line/login` | LINEログインリダイレクトURL取得 |
| GET | `/api/line/callback` | LINEログインコールバック |

---

## 🔔 LINE連携

- `/api/line/login` を叩くとLINEログイン画面へ遷移
- 承認後、自動的にLINE ID がユーザーに紐づけられ、通知対象になります

---

## 🧪 テスト実行

```bash
# テスト用コンテナを起動
docker compose -f docker-compose.test.yml up -d

# テスト実行
docker exec app php artisan test --env=testing
```

---

## ⏰ 通知ジョブ

- タスクの `due_date - remind_before_minutes` が現在時刻を過ぎたら通知
- 毎分 `SendTaskDueReminders` ジョブが走り、通知処理を実行
- 通知は1回限り（`is_reminded = true` に更新）

---

## 📝 使用技術

- Laravel 12
- Sanctum
- MySQL
- Vue 3 (Vite)
- Docker / Docker Compose
- LINE Messaging API
- Pest（テストフレームワーク）

---

## 🕒 所要時間

約12時間

---

## 🛠 注意事項

- `.env` と `.env.testing` をそれぞれ環境に合わせて用意してください
- LINE通知を使うには LINE Developers の設定が必要です



