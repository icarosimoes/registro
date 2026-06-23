import { AppLayout } from "@/components/app-layout";
import { OperationalModule } from "@/components/operational-module";
import { currentTenantUser, tenantFetch } from "@/lib/api";
import { moduleDefinitions } from "@/lib/module-definitions";
import { redirect } from "next/navigation";

export default async function ProcedimentosPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | undefined>>;
}) {
  const query = await searchParams;
  const definition = moduleDefinitions["cadastros/procedimentos"];

  try {
    const user = await currentTenantUser();
    let hydratedDefinition = definition;

    type ProcedureItem = { id: number; name: string; link: string | null; file: string | null; updated_at: string };
    type ProcedurePage = { items: ProcedureItem[]; total: number; page: number; page_size: number };

    try {
      const pg = Math.max(1, parseInt(query.page ?? "1", 10) || 1);
      const search = query.search ?? "";
      const searchParam = search ? `&search=${encodeURIComponent(search)}` : "";
      const data = await tenantFetch<ProcedurePage>(`/procedures?page=${pg}&page_size=20${searchParam}`);
      hydratedDefinition = {
        ...definition,
        source: "api",
        records: data.items.map((item) => ({
          id: item.id,
          title: item.name,
          category: "Procedimento",
          owner: "Administração",
          status: "Ativo",
          description: item.link ?? undefined,
          updatedAt: new Intl.DateTimeFormat("pt-BR").format(new Date(item.updated_at)),
        })),
        serverPagination: { total: data.total, page: data.page, pageSize: data.page_size, search },
      };
    } catch (error) {
      if (error instanceof Error && error.message === "unauthorized") throw error;
    }

    return (
      <AppLayout user={user}>
        <OperationalModule definition={hydratedDefinition} user={user} />
      </AppLayout>
    );
  } catch (error) {
    if (error instanceof Error && error.message === "unauthorized")
      redirect("/login");
    throw error;
  }
}
