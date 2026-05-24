# Yii3 Demo — Task Tracker

This is a minimal task tracker built with **Yii 3** to demonstrate the following packages:

- [`yii1x/active-record`](https://github.com/yiiex/active-record) — Active Record ORM
- [`yii1x/validator`](https://github.com/yiiex/validator) — validation library
- [`yiiex/inertiajs`](https://github.com/yiiex/inertiajs) — Inertia.js server adapter

The app is intentionally kept **minimal** — no complex permission system, no overengineering. Just enough to showcase real-world usage.

**Key concepts demonstrated:**

- **DataProvider** — paginated data sources for tables with built-in filter and action support
- **ActionProvider** — declarative action buttons (edit, delete, etc.) with visibility callbacks
- **Filter** — reusable query filters via PHP attributes
- **Caster** — value casting on get/set (DateTime, Duration)

**Frontend:** Inertia.js + Vue 3 + shadcn-vue (Tailwind CSS).

## 🚀 Quick start

```bash
docker-compose up -d
```

App will be available at **http://localhost:8876**.

```bash
php yii migrate up
php yii user
```

The `user` command will guide you through creating the first admin user.
