# Vue Keycloak App

Vue 3 + Vite + TypeScript + TailwindCSS + Keycloak SSO

## Tech Stack

- **Vite** - Build tool
- **Vue 3** - Frontend framework
- **TypeScript** - Type safety
- **Vue Router** - Routing
- **Pinia** - State management
- **TailwindCSS** - Styling
- **Keycloak** - SSO Authentication

## Keycloak Configuration

- **URL**: `https://sso.shopsthai.com`
- **Realm**: `shopsthai.app`
- **Client ID**: `shopsthai-web1`

## Setup

```bash
# Install dependencies
bun install

# Start development server
bun run dev

# Build for production
bun run build

# Preview production build
bun run preview
```

## Project Structure

```
src/
├── App.vue           # Main app component
├── main.ts           # App entry point
├── keycloak.ts       # Keycloak configuration
├── style.css         # Global styles (TailwindCSS)
├── router/
│   └── index.ts      # Vue Router configuration
├── stores/
│   ├── index.ts      # Store exports
│   └── auth.ts       # Auth store (Keycloak)
└── views/
    ├── HomeView.vue  # Home page
    └── ProfileView.vue # Profile page
```
