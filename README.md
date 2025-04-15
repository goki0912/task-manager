# 📝 簡易タスク管理アプリケーション

Laravel12 + Vue3 によるタスク管理アプリケーションです。  
ユーザー登録・ログイン後、自身のタスクを作成・編集・削除でき、他ユーザーをタスクにアサイン可能です。  
LINE通知によるリマインダー機能付き。
[友だち登録はこちら](https://lin.ee/kWtISYV)

---

## 🚀 機能一覧

- ユーザー認証（登録 / ログイン / ログアウト）
- タスクCRUD（title / description / is_done / due_date / remind_before_minutes）
- 他ユーザーへのタスクアサイン
- タスク期限のLINE通知（通知時刻カスタマイズ可）
- 認可ポリシーによるアクセス制限（他人のタスクは見られない）
- フロントエンド：Vue3（最低限の構成）

---

## 🧪 テスト

- Pest によるFeatureテスト・認可ポリシーテスト・リマインド通知ジョブテストなどを完備
- テスト用DB（`task_manager_testing`）を使って環境を分離

---

## 📦 セットアップ手順（Docker使用）

```bash
# Laravel バックエンドの準備
cd task-manager
cp .env.example .env (もしくは共有された.envファイルを使用)

# ↓ 以下の環境変数を .env に追記（提供された値を入力）
echo '
LINE_LOGIN_CHANNEL_ID= # 入力してください
LINE_CLIENT_SECRET= # 入力してください
LINE_REDIRECT_URI=http://localhost:8000/api/line/callback
LINE_CHANNEL_ACCESS_TOKEN= # 入力してください
' >> .env

make init # ビルド&起動&マイグレーション
make workers # ワーカー起動
```
```bash
# フロントエンドの起動（Vue）
cd vue
npm install
npm run dev
```
## 🧪 テスト実行

```bash
make test
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

## 📮 エンドポイント仕様

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

## ⏰ リマインド通知ジョブ

- タスクの `due_date - remind_before_minutes` が現在時刻を過ぎたら通知
- 毎分 `SendTaskDueReminders` ジョブが走り、通知処理を実行
- 通知は1回限り（`is_reminded = true` に更新）

---

## 📝 使用技術

- Laravel 12
- Sanctum（認証ライブラリ）
- Pest（テストフレームワーク）
- MySQL
- Vue 3 (Vite)
- Docker / Docker Compose
- LINE Messaging API

---

## 🕒 所要時間

約16時間

---



