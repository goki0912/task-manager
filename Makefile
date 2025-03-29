# テスト用コンテナを起動（初回や必要なときだけ）
test-up:
	docker-compose -f docker-compose.test.yml up --build -d

# テスト用コンテナを停止
test-down:
	docker-compose -f docker-compose.test.yml down

# テストDBにマイグレーション
test-migrate:
	docker-compose -f docker-compose.test.yml exec app_test php artisan migrate --env=testing

# テスト実行（Pest）
test:
	docker-compose -f docker-compose.test.yml exec app_test php artisan test --env=testing

# 設定キャッシュをクリア
test-clear:
	docker-compose -f docker-compose.test.yml exec app_test php artisan config:clear
