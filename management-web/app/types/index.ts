export interface User {
  id: number
  name: string
  email: string
  phone: string | null
  join_date: string | null
  is_active: boolean
  department?: Department | null
  position?: Position | null
  roles: string[]
  permissions: string[]
  created_at: string
}

export interface Department {
  id: number
  name: string
  code: string
  manager?: User | null
  positions?: Position[]
  users_count?: number
  created_at: string
}

export interface Position {
  id: number
  title: string
  department_id: number
}

export interface DashboardStats {
  total_users: number
  active_users: number
  inactive_users: number
  total_departments: number
  total_positions: number
}

export interface ApiResponse<T> {
  data: T
}

export interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: PaginationMeta
}
