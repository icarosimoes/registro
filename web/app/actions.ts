"use server";

import { cookies } from "next/headers";
import { redirect } from "next/navigation";

const apiUrl = process.env.API_URL ?? "http://localhost:8000/api/v1";

interface LoginResult {
  ok: boolean;
  error?: string;
  multi_tenant?: boolean;
  tenants?: { id: number; name: string }[];
}

export async function loginAction(
  email: string,
  password: string,
  companyId?: number,
): Promise<LoginResult> {
  const body: Record<string, unknown> = { email, password };
  if (companyId) body.company_id = companyId;

  const response = await fetch(`${apiUrl}/auth/login`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(body),
    cache: "no-store",
  });

  if (response.status === 422) {
    const data = await response.json();
    if (data.detail?.code === "multi_tenant") {
      return {
        ok: false,
        multi_tenant: true,
        tenants: data.detail.tenants,
      };
    }
  }

  if (!response.ok) {
    return { ok: false, error: "E-mail ou senha inválidos." };
  }

  const data = (await response.json()) as {
    access_token: string;
    refresh_token: string;
    expires_in: number;
  };
  const jar = await cookies();
  jar.set("tenant_token", data.access_token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    maxAge: data.expires_in,
  });
  jar.set("tenant_refresh_token", data.refresh_token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    maxAge: 60 * 60 * 24 * 7,
  });
  return { ok: true };
}

export async function logoutAction() {
  const jar = await cookies();
  jar.delete("tenant_token");
  jar.delete("tenant_refresh_token");
  redirect("/login");
}

async function tryRefresh(): Promise<string | null> {
  const jar = await cookies();
  const refreshToken = jar.get("tenant_refresh_token")?.value;
  if (!refreshToken) return null;

  const response = await fetch(`${apiUrl}/auth/refresh`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ refresh_token: refreshToken }),
    cache: "no-store",
  });
  if (!response.ok) return null;

  const data = (await response.json()) as {
    access_token: string;
    refresh_token: string;
    expires_in: number;
  };
  jar.set("tenant_token", data.access_token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    maxAge: data.expires_in,
  });
  jar.set("tenant_refresh_token", data.refresh_token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    maxAge: 60 * 60 * 24 * 7,
  });
  return data.access_token;
}

async function authedFetch(path: string, init?: RequestInit): Promise<Response> {
  const jar = await cookies();
  let token = jar.get("tenant_token")?.value;
  if (!token) {
    token = await tryRefresh() ?? undefined;
    if (!token) throw new Error("unauthorized");
  }
  const response = await fetch(`${apiUrl}${path}`, {
    ...init,
    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json", ...init?.headers },
    cache: "no-store",
  });
  if (response.status === 401) {
    const newToken = await tryRefresh();
    if (!newToken) throw new Error("unauthorized");
    return fetch(`${apiUrl}${path}`, {
      ...init,
      headers: { Authorization: `Bearer ${newToken}`, "Content-Type": "application/json", ...init?.headers },
      cache: "no-store",
    });
  }
  return response;
}

export interface FiscalRequestPayload {
  request_type: string;
  title: string;
  apartment?: string;
  requester: string;
  description?: string;
  status?: string;
  payload?: Record<string, unknown>;
}

interface MutationResult {
  ok: boolean;
  error?: string;
  data?: Record<string, unknown>;
}

export async function createFiscalRequestAction(body: FiscalRequestPayload): Promise<MutationResult> {
  const response = await authedFetch("/fiscal-requests", {
    method: "POST",
    body: JSON.stringify(body),
  });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao criar solicitação." };
  }
  return { ok: true, data: await response.json() };
}

export async function updateFiscalRequestAction(
  id: number,
  body: Partial<FiscalRequestPayload>,
): Promise<MutationResult> {
  const response = await authedFetch(`/fiscal-requests/${id}`, {
    method: "PATCH",
    body: JSON.stringify(body),
  });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao atualizar solicitação." };
  }
  return { ok: true, data: await response.json() };
}

export async function deleteFiscalRequestAction(id: number): Promise<MutationResult> {
  const response = await authedFetch(`/fiscal-requests/${id}`, { method: "DELETE" });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao excluir solicitacao." };
  }
  return { ok: true };
}

export interface OccurrencePayload {
  title: string;
  description?: string;
  unit?: string;
  deadline?: string;
  status?: number;
  sector_id?: number;
  location_id?: number;
  owner_user_id?: number;
  notify_user_ids?: number[];
}

export async function createOccurrenceAction(body: OccurrencePayload): Promise<MutationResult> {
  const response = await authedFetch("/occurrences", {
    method: "POST",
    body: JSON.stringify(body),
  });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao criar ocorrencia." };
  }
  return { ok: true, data: await response.json() };
}

export async function updateOccurrenceAction(
  id: number,
  body: Partial<OccurrencePayload>,
): Promise<MutationResult> {
  const response = await authedFetch(`/occurrences/${id}`, {
    method: "PATCH",
    body: JSON.stringify(body),
  });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao atualizar ocorrencia." };
  }
  return { ok: true, data: await response.json() };
}

export async function deleteOccurrenceAction(id: number): Promise<MutationResult> {
  const response = await authedFetch(`/occurrences/${id}`, { method: "DELETE" });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao excluir ocorrencia." };
  }
  return { ok: true };
}

export interface UserPayload {
  name: string;
  email: string;
  phone?: string;
  password?: string;
  role_id?: number | null;
  active?: boolean;
}

export async function createUserAction(body: UserPayload): Promise<MutationResult> {
  const response = await authedFetch("/users", { method: "POST", body: JSON.stringify(body) });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    if (response.status === 409) return { ok: false, error: "E-mail já cadastrado." };
    return { ok: false, error: "Erro ao criar usuário." };
  }
  return { ok: true, data: await response.json() };
}

export async function updateUserAction(id: number, body: Partial<UserPayload>): Promise<MutationResult> {
  const response = await authedFetch(`/users/${id}`, { method: "PATCH", body: JSON.stringify(body) });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao atualizar usuário." };
  }
  return { ok: true, data: await response.json() };
}

export async function deleteUserAction(id: number): Promise<MutationResult> {
  const response = await authedFetch(`/users/${id}`, { method: "DELETE" });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    if (response.status === 400) return { ok: false, error: "Não é possível excluir seu próprio usuário." };
    return { ok: false, error: "Erro ao excluir usuário." };
  }
  return { ok: true };
}

export interface RegistryPayload {
  name: string;
  category: string;
}

export async function createRegistryAction(body: RegistryPayload): Promise<MutationResult> {
  const response = await authedFetch("/registries", { method: "POST", body: JSON.stringify(body) });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao criar cadastro." };
  }
  return { ok: true, data: await response.json() };
}

export async function updateRegistryAction(id: number, body: { name: string }, category: string): Promise<MutationResult> {
  const response = await authedFetch(`/registries/${id}?category=${encodeURIComponent(category)}`, { method: "PATCH", body: JSON.stringify(body) });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao atualizar cadastro." };
  }
  return { ok: true, data: await response.json() };
}

export async function deleteRegistryAction(id: number, category: string): Promise<MutationResult> {
  const response = await authedFetch(`/registries/${id}?category=${encodeURIComponent(category)}`, { method: "DELETE" });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao excluir cadastro." };
  }
  return { ok: true };
}

export interface ModuleRecordPayload {
  title: string;
  description?: string;
  category?: string;
  status?: string;
  owner_user_id?: number;
  notify_user_ids?: number[];
}

export async function createModuleRecordAction(moduleSlug: string, body: ModuleRecordPayload): Promise<MutationResult> {
  const response = await authedFetch(`/modules/${moduleSlug}`, { method: "POST", body: JSON.stringify(body) });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao criar registro." };
  }
  return { ok: true, data: await response.json() };
}

export async function updateModuleRecordAction(moduleSlug: string, id: number, body: Partial<ModuleRecordPayload>): Promise<MutationResult> {
  const response = await authedFetch(`/modules/${moduleSlug}/${id}`, { method: "PATCH", body: JSON.stringify(body) });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao atualizar registro." };
  }
  return { ok: true, data: await response.json() };
}

export async function deleteModuleRecordAction(moduleSlug: string, id: number): Promise<MutationResult> {
  const response = await authedFetch(`/modules/${moduleSlug}/${id}`, { method: "DELETE" });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao excluir registro." };
  }
  return { ok: true };
}

export interface TimelineEntry {
  id: number;
  event_type: string;
  user: string;
  message: string | null;
  changes: Record<string, { from: string; to: string }> | null;
  created_at: string;
}

export async function fetchTimeline(entityType: string, entityId: number): Promise<TimelineEntry[]> {
  const response = await authedFetch(`/timeline/${entityType}/${entityId}`);
  if (!response.ok) return [];
  const data = await response.json();
  return data.items ?? [];
}

export async function addCommentAction(entityType: string, entityId: number, message: string): Promise<MutationResult> {
  const response = await authedFetch(`/timeline/${entityType}/${entityId}/comment`, {
    method: "POST",
    body: JSON.stringify({ message }),
  });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao adicionar comentário." };
  }
  return { ok: true, data: await response.json() };
}

export interface NotificationItem {
  id: number;
  title: string;
  body: string | null;
  category: string;
  entity_type: string | null;
  entity_id: number | null;
  read_at: string | null;
  created_at: string;
}

export interface NotificationListResult {
  items: NotificationItem[];
  total: number;
  unread: number;
  page: number;
  page_size: number;
}

export async function fetchNotifications(page = 1, unreadOnly = false): Promise<NotificationListResult> {
  const params = new URLSearchParams({ page: String(page), page_size: "20" });
  if (unreadOnly) params.set("unread_only", "true");
  const response = await authedFetch(`/notifications?${params}`);
  if (!response.ok) return { items: [], total: 0, unread: 0, page: 1, page_size: 20 };
  return response.json();
}

export async function markNotificationRead(id: number): Promise<void> {
  await authedFetch(`/notifications/${id}/read`, { method: "PATCH" });
}

export async function markAllNotificationsRead(): Promise<void> {
  await authedFetch("/notifications/read-all", { method: "POST" });
}

export async function updateProfileAction(body: { name?: string; phone?: string; password?: string }): Promise<MutationResult> {
  const response = await authedFetch("/users/me", { method: "PATCH", body: JSON.stringify(body) });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    if (response.status === 422) return { ok: false, error: "Nenhum campo alterado." };
    return { ok: false, error: "Erro ao atualizar perfil." };
  }
  return { ok: true, data: await response.json() };
}

// --- Attachments ---

export interface AttachmentItem {
  id: number;
  entity_type: string;
  entity_id: number;
  filename: string;
  content_type: string;
  size_bytes: number;
  uploaded_by_user_id: number;
  created_at: string;
}

export async function uploadAttachmentAction(
  entityType: string,
  entityId: number,
  file: File,
): Promise<MutationResult> {
  const jar = await cookies();
  let token = jar.get("tenant_token")?.value;
  if (!token) {
    token = (await tryRefresh()) ?? undefined;
    if (!token) throw new Error("unauthorized");
  }

  const formData = new FormData();
  formData.append("file", file);
  const params = new URLSearchParams({
    entity_type: entityType,
    entity_id: String(entityId),
  });

  let response = await fetch(
    `${apiUrl}/attachments?${params}`,
    {
      method: "POST",
      headers: { Authorization: `Bearer ${token}` },
      body: formData,
      cache: "no-store",
    },
  );

  if (response.status === 401) {
    const newToken = await tryRefresh();
    if (!newToken) throw new Error("unauthorized");
    response = await fetch(`${apiUrl}/attachments?${params}`, {
      method: "POST",
      headers: { Authorization: `Bearer ${newToken}` },
      body: formData,
      cache: "no-store",
    });
  }

  if (!response.ok) {
    const data = await response.json().catch(() => ({}));
    return {
      ok: false,
      error: data?.detail?.message ?? "Erro ao enviar anexo.",
    };
  }
  return { ok: true, data: await response.json() };
}

export async function fetchAttachments(
  entityType: string,
  entityId: number,
): Promise<AttachmentItem[]> {
  const params = new URLSearchParams({
    entity_type: entityType,
    entity_id: String(entityId),
  });
  const response = await authedFetch(`/attachments?${params}`);
  if (!response.ok) return [];
  const data = await response.json();
  return data.items ?? [];
}

export async function deleteAttachmentAction(
  id: number,
): Promise<MutationResult> {
  const response = await authedFetch(`/attachments/${id}`, {
    method: "DELETE",
  });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao excluir anexo." };
  }
  return { ok: true };
}

export function getAttachmentDownloadUrl(id: number): string {
  return `${apiUrl}/attachments/${id}/download`;
}

export interface EvolutionSettings {
  has_credentials: boolean;
  api_url?: string | null;
  instance?: string | null;
}

export async function getEvolutionSettings(): Promise<EvolutionSettings> {
  const response = await authedFetch("/settings/evolution");
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { has_credentials: false };
  }
  return response.json();
}

export async function saveEvolutionSettings(body: { api_url: string; api_key: string; instance: string }): Promise<MutationResult> {
  const response = await authedFetch("/settings/evolution", { method: "POST", body: JSON.stringify(body) });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao salvar configurações da Evolution." };
  }
  return { ok: true, data: await response.json() };
}

export interface BrevoSettings {
  has_credentials: boolean;
  from_address?: string | null;
  from_name?: string | null;
}

export async function getBrevoSettings(): Promise<BrevoSettings> {
  const response = await authedFetch("/settings/brevo");
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { has_credentials: false };
  }
  return response.json();
}

export async function saveBrevoSettings(body: { api_key: string; from_address: string; from_name: string }): Promise<MutationResult> {
  const response = await authedFetch("/settings/brevo", { method: "POST", body: JSON.stringify(body) });
  if (!response.ok) {
    if (response.status === 401) throw new Error("unauthorized");
    return { ok: false, error: "Erro ao salvar configurações do Brevo." };
  }
  return { ok: true, data: await response.json() };
}

export interface UserOption {
  id: number;
  name: string;
  email: string;
}

export async function searchUsers(q: string): Promise<UserOption[]> {
  const response = await authedFetch(`/users/search?q=${encodeURIComponent(q)}`);
  if (!response.ok) return [];
  return response.json();
}
