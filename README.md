# Lily

服薬履歴管理システム（Medication History Tracking System）

## 前提条件

- [docker-toolchains](https://github.com/NewWorldOrg/docker-toolchains) が稼働していること
- [Task](https://taskfile.dev/) がインストールされていること

## セットアップ

### 1. 設定ファイルの準備

```shell
task init_local
```

`.env.example` → `.env`、`compose.example.yml` → `compose.yml` がコピーされます。
必要に応じて `compose.yml` を環境に合わせて編集してください。

### 2. Docker イメージのビルド

```shell
task docker_build
```

### 3. コンテナの起動

```shell
task up
```

### 4. アプリケーションのセットアップ

```shell
task setup
```

Composer install、鍵生成、マイグレーション、シーディング等が一括で実行されます。

セットアップ完了後、`lily.localhost/admin/auth/login` にアクセスできれば成功です。

## よく使うコマンド

| コマンド | 説明 |
|---------|------|
| `task up` | コンテナ起動 |
| `task down` | コンテナ停止 |
| `task bash` | アプリコンテナにSSH |
| `task node-ssh` | JSコンテナにSSH |
| `task migrate` | マイグレーション実行 |
| `task migrate_seed` | マイグレーション（fresh）+ シーディング |
| `task cache_clear` | キャッシュクリア |
| `task swagger` | Swagger ドキュメント生成 |
| `task start_discord_bot` | Discord Bot 起動 |
| `task ide_helper` | IDE Helper 生成 |

## テスト

```shell
task test_unit            # ユニットテスト
task test_feature         # フィーチャーテスト
task test_db_integration  # DBインテグレーションテスト
```

テスト用DBのシーディング:

```shell
task test_seed
```

## 技術スタック

- PHP 8.4 / Laravel 12
- MySQL 9.1
- Vite 6 / jQuery 3.7
- Docker (PHP Unit server, MySQL, Node 22)
- Traefik (リバースプロキシ)
