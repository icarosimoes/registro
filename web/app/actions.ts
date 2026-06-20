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

  const data = (await response.json()) as { access_token: string; expires_in: number };
  (await cookies()).set("tenant_token", data.access_token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    maxAge: data.expires_in,
  });
  return { ok: true };
}

export async function logoutAction() {
  (await cookies()).delete("tenant_token");
  redirect("/login");
}

async function authedFetch(path: string, init?: RequestInit): Promise<Response> {
  const token = (await cookies()).get("tenant_token")?.value;
  if (!token) throw new Error("unauthorized");
  return fetch(`${apiUrl}${path}`, {
    ...init,
    headers: { Authorization: `Bearer ${token}`, "Content-Type": "application/json", ...init?.headers },
    cache: "no-store",
  });
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
