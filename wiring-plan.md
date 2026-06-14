# Frontend-Backend Wiring Plan

## Current State

```
Frontend (React) ──→ localStorage (100% of data)
                 ──→ Mock Firebase (no-op stub, never reaches real Firebase)
                 ──→ Gemini API (only external call)
Backend (Laravel) ──→ SQLite (completely unused by frontend)
```

No Supabase exists. Firebase is a stale dependency — `services/firebase.ts` is a mock stub returning empty data, `src/firebase.ts` has real config but is never imported.

---

## Phase 1: Remove Firebase (clean up)

| Step | What | Files |
|---|---|---|
| 1.1 | Uninstall npm package | `npm uninstall firebase` |
| 1.2 | Delete mock Firebase | `services/firebase.ts` |
| 1.3 | Delete real Firebase config | `src/firebase.ts` |
| 1.4 | Delete Firebase Hosting config | `firebase.json` |
| 1.5 | Delete Firebase Data Connect scaffolding | `dataconnect/` |
| 1.6 | Delete generated Data Connect code | `src/dataconnect-generated/` |
| 1.7 | Delete Firebase cache | `.firebase/` |
| 1.8 | Remove unused imports in AuthContext | `context/AuthContext.tsx` (imports from `services/firebase`) |
| 1.9 | Delete debug page | `src/components/TestDB.tsx` |
| 1.10 | Delete scaffolding artifacts | `metadata.json`, `prop.md` |

---

## Phase 2: Create API Client

| Step | What | Details |
|---|---|---|
| 2.1 | Create `services/api.ts` | Axios instance with base URL from `VITE_API_URL` env var, Bearer token interceptor |
| 2.2 | Add `VITE_API_URL` to `.env.local` | `VITE_API_URL=http://localhost:8000/api` |
| 2.3 | Add `VITE_API_URL` to vite.config.ts define | So it's available as `process.env.VITE_API_URL` |

---

## Phase 3: Rewrite AuthContext (Sanctum auth)

Replace hardcoded login with real Sanctum flow:

| Step | What | Details |
|---|---|---|
| 3.1 | Login sends `POST /api/login` | Returns `{ token, user }` |
| 3.2 | Store token in `localStorage` | Key: `wfp_token` |
| 3.3 | Attach `Authorization: Bearer {token}` to all API requests | Via axios interceptor |
| 3.4 | Session check on app load | Try `GET /api/user` with stored token; if 401, redirect to login |
| 3.5 | Logout calls `POST /api/logout` | Deletes token server-side, clears localStorage |
| 3.6 | Remove hardcoded credentials | Delete the admin/Admin@12345 fallback |

---

## Phase 4: Replace DB Calls with API Calls (page by page)

### 4a — EmployeeManagement.tsx (users CRUD)

| Frontend call | Replace with |
|---|---|
| `DB.users.getAll()` | `GET /api/users` |
| `DB.users.getById(id)` | `GET /api/users/{id}` |
| `DB.users.add(user)` | `POST /api/users` |
| `DB.users.update(user)` | `PUT /api/users/{id}` |
| `DB.users.delete(id, user)` | `DELETE /api/users/{id}` |
| `DB.requests.getAll()` | `GET /api/credential-requests` |
| `DB.requests.add(req)` | `POST /api/credential-requests` |
| `DB.requests.update(req)` | `PUT /api/credential-requests/{id}` |

### 4b — Tasks.tsx

| Frontend call | Replace with |
|---|---|
| `DB.tasks.getAll()` | `GET /api/tasks` |
| `DB.tasks.add(task)` | `POST /api/tasks` |
| `DB.tasks.update(task)` | `PUT /api/tasks/{id}` |
| `DB.tasks.delete(id, user)` | `DELETE /api/tasks/{id}` |
| `DB.logs.add(log)` | `POST /api/activity-logs` |

### 4c — Dashboard.tsx

| Frontend call | Replace with |
|---|---|
| `DB.tasks.getAll()` | `GET /api/dashboard/stats` (already built) |
| `DB.users.getAll()` | `GET /api/dashboard/stats` |
| `DB.attendance.getAll()` | `GET /api/dashboard/stats` |
| `DB.logs.getAll()` | `GET /api/activity-logs?per_page=5` |

### 4d — Attendance.tsx

| Frontend call | Replace with |
|---|---|
| `DB.attendance.getAll()` | `GET /api/attendance/history` |
| `DB.attendance.add(record)` | `POST /api/attendance/check-in` |
| `DB.attendance.update(record)` | `PUT /api/attendance/{id}/check-out` |

### 4e — Finance.tsx

| Frontend call | Replace with |
|---|---|
| `DB.finance.getAll()` | `GET /api/records` |
| `DB.finance.add(record)` | `POST /api/records` |
| `DB.finance.delete(id, user)` | `DELETE /api/records/{id}` |
| `DB.logs.add(log)` | `POST /api/activity-logs` |

### 4f — Reports.tsx

| Frontend call | Replace with |
|---|---|
| `DB.users.getAll()` | `GET /api/users` |
| `DB.attendance.getAll()` | `GET /api/attendance` |
| `DB.logs.add(log)` | `POST /api/activity-logs` |

### 4g — RecycleBin.tsx

| Frontend call | Replace with |
|---|---|
| `DB.recycleBin.getAll()` | `GET /api/recycle-bin` |
| `DB.recycleBin.restore(itemId)` | `POST /api/recycle-bin/{model}/{id}/restore` |
| `DB.recycleBin.hardDelete(itemId)` | `DELETE /api/recycle-bin/{model}/{id}/force` |

### 4h — Profile.tsx

| Frontend call | Replace with |
|---|---|
| `DB.logs.add(log)` | `POST /api/activity-logs` |
| `DB.requests.add(req)` | `POST /api/credential-requests` |
| Image upload | `POST /api/users/{id}` (multipart, existing) |

### 4i — Chat.tsx and Sidebar.tsx

| Frontend call | Replace with |
|---|---|
| `DB.users.getAll()` | `GET /api/users` |
| `DB.messages.*` | Requires **new backend endpoints** (see Phase 5) |

---

## Phase 5: Build Missing Backend Endpoints

### 5a — Messages / Chat
| Endpoint | Purpose |
|---|---|
| `GET /api/messages` | List conversations for current user |
| `GET /api/messages/{user}` | Get conversation with specific user |
| `POST /api/messages` | Send a message |
| `PUT /api/messages/{id}/read` | Mark message as read |

### 5b — Credential Requests
| Endpoint | Purpose |
|---|---|
| `GET /api/credential-requests` | List requests (ADMIN/HR sees all, EMPLOYEE sees own) |
| `POST /api/credential-requests` | Submit a request |
| `PUT /api/credential-requests/{id}` | Approve/reject request |

---

## Phase 6: Clean Up & Polish

| Step | What |
|---|---|
| 6.1 | Delete `services/db.ts` (or keep as fallback) |
| 6.2 | Remove unused localStorage init/seed data |
| 6.3 | Add `VITE_API_URL` proxy in vite.config.ts for dev |
| 6.4 | Test all flows end-to-end |

---

## Estimated Effort

| Phase | Files touched | Difficulty |
|---|---|---|
| Phase 1 (Remove Firebase) | ~10 | Easy |
| Phase 2 (API Client) | 3 | Easy |
| Phase 3 (Auth rewrite) | 2 | Medium |
| Phase 4 (Replace DB calls) | 10 pages | Medium |
| Phase 5 (Missing endpoints) | 2 new features | Medium |
| Phase 6 (Clean up) | 2-3 | Easy |

**Total: ~6-8 hours of work**
