import { AppLayout } from "@/components/app-layout";
import { OperationalModule } from "@/components/operational-module";
import { currentTenantUser, tenantFetch } from "@/lib/api";
import { moduleDefinitions } from "@/lib/module-definitions";
import { notFound, redirect } from "next/navigation";

const GENERIC_MODULES = new Set(["reunioes", "relatorios-turno", "inspecoes", "diarios-obra", "manutencao", "mural"]);

export default async function ModulePage({ params, searchParams }: { params: Promise<{ module: string }>; searchParams: Promise<Record<string, string | undefined>> }) {
  const { module } = await params;
  const query = await searchParams;
  const definition = moduleDefinitions[module];
  if (!definition) notFound();

  try {
    const user = await currentTenantUser();
    let hydratedDefinition = definition;

    if (module === "ocorrencias") {
      try {
        type OccurrencePage = {
          items: Array<{ id: number; legacy_id: number; title: string; description: string | null; category: string; owner: string; status: string; updated_at: string }>;
          total: number;
          page: number;
          page_size: number;
        };
        const pg = Math.max(1, parseInt(query.page ?? "1", 10) || 1);
        const search = query.search ?? "";
        const searchParam = search ? `&search=${encodeURIComponent(search)}` : "";
        const data = await tenantFetch<OccurrencePage>(`/occurrences?page=${pg}&page_size=20${searchParam}`);
        hydratedDefinition = {
          ...definition,
          source: "api",
          records: data.items.map((item) => ({
              id: item.id,
              title: item.title,
              description: item.description ?? undefined,
              category: item.category,
              owner: item.owner,
              status: item.status,
              updatedAt: new Intl.DateTimeFormat("pt-BR").format(new Date(item.updated_at)),
          })),
          serverPagination: { total: data.total, page: data.page, pageSize: data.page_size, search },
        };
      } catch (error) {
        if (error instanceof Error && error.message === "unauthorized") throw error;
      }
    } else if (module === "solicitacoes-fiscais") {
      type FiscalRequestItem = {
        id: number;
        protocol: string;
        request_type: string;
        title?: string | null;
        apartment: string | null;
        requester: string;
        description?: string | null;
        status: string;
        payload: Record<string, unknown>;
        created_at: string;
        updated_at: string;
      };
      type FiscalRequestPage = { items: FiscalRequestItem[] };
      try {
        const response = await tenantFetch<FiscalRequestPage>("/fiscal-requests");
        hydratedDefinition = {
          ...definition,
          source: "api",
          records: response.items.map((item) => ({
            ...item.payload,
            id: item.id,
            title: item.title || `${item.request_type}${item.apartment ? ` · UH ${item.apartment}` : ""}`,
            category: item.request_type,
            owner: item.requester,
            status: item.status,
            requestType: item.request_type,
            apartment: item.apartment ?? undefined,
            description: item.description || String(item.payload.observations ?? item.payload.description ?? ""),
            updatedAt: new Intl.DateTimeFormat("pt-BR", {
              dateStyle: "short",
              timeStyle: "short",
            }).format(new Date(item.updated_at)),
          })),
        };
      } catch (error) {
        if (error instanceof Error && error.message === "unauthorized") throw error;
      }
    } else if (module === "usuarios") {
      type UserItem = {
        id: number;
        name: string;
        email: string;
        role_name: string | null;
        active: boolean;
        updated_at: string;
      };
      type UserPage = { items: UserItem[]; total: number; page: number; page_size: number };
      try {
        const pg = Math.max(1, parseInt(query.page ?? "1", 10) || 1);
        const search = query.search ?? "";
        const searchParam = search ? `&search=${encodeURIComponent(search)}` : "";
        const data = await tenantFetch<UserPage>(`/users?page=${pg}&page_size=20${searchParam}`);
        hydratedDefinition = {
          ...definition,
          source: "api",
          records: data.items.map((item) => ({
            id: item.id,
            title: item.name,
            category: item.role_name ?? "Sem cargo",
            owner: item.email,
            status: item.active ? "Ativo" : "Inativo",
            updatedAt: new Intl.DateTimeFormat("pt-BR").format(new Date(item.updated_at)),
          })),
          serverPagination: { total: data.total, page: data.page, pageSize: data.page_size, search },
        };
      } catch (error) {
        if (error instanceof Error && error.message === "unauthorized") throw error;
      }
    } else if (module === "cadastros") {
      type RegistryItem = { id: number; name: string; category: string; updated_at: string };
      type RegistryPage = { items: RegistryItem[]; total: number; page: number; page_size: number };
      try {
        const pg = Math.max(1, parseInt(query.page ?? "1", 10) || 1);
        const search = query.search ?? "";
        const searchParam = search ? `&search=${encodeURIComponent(search)}` : "";
        const data = await tenantFetch<RegistryPage>(`/registries?page=${pg}&page_size=20${searchParam}`);
        hydratedDefinition = {
          ...definition,
          source: "api",
          records: data.items.map((item) => ({
            id: item.id,
            title: item.name,
            category: item.category,
            owner: "Administração",
            status: "Ativo",
            updatedAt: new Intl.DateTimeFormat("pt-BR").format(new Date(item.updated_at)),
          })),
          serverPagination: { total: data.total, page: data.page, pageSize: data.page_size, search },
        };
      } catch (error) {
        if (error instanceof Error && error.message === "unauthorized") throw error;
      }
    } else if (GENERIC_MODULES.has(module)) {
      type ModuleRecordItem = {
        id: number;
        title: string;
        description: string | null;
        category: string | null;
        owner: string;
        status: string;
        updated_at: string;
      };
      type ModuleRecordPage = { items: ModuleRecordItem[]; total: number; page: number; page_size: number };
      try {
        const pg = Math.max(1, parseInt(query.page ?? "1", 10) || 1);
        const search = query.search ?? "";
        const searchParam = search ? `&search=${encodeURIComponent(search)}` : "";
        const data = await tenantFetch<ModuleRecordPage>(`/modules/${module}?page=${pg}&page_size=20${searchParam}`);
        hydratedDefinition = {
          ...definition,
          source: "api",
          records: data.items.map((item) => ({
            id: item.id,
            title: item.title,
            description: item.description ?? undefined,
            category: item.category ?? "Geral",
            owner: item.owner,
            status: item.status,
            updatedAt: new Intl.DateTimeFormat("pt-BR").format(new Date(item.updated_at)),
          })),
          serverPagination: { total: data.total, page: data.page, pageSize: data.page_size, search },
        };
      } catch (error) {
        if (error instanceof Error && error.message === "unauthorized") throw error;
      }
    }

    return <AppLayout user={user}><OperationalModule definition={hydratedDefinition} user={user} /></AppLayout>;
  } catch (error) {
    if (error instanceof Error && error.message === "unauthorized") redirect("/login");
    throw error;
  }
}
