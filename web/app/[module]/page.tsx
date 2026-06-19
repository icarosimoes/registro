import { OperationalModule } from "@/components/operational-module";
import { currentTenantUser, tenantFetch } from "@/lib/api";
import { moduleDefinitions } from "@/lib/module-definitions";
import { notFound, redirect } from "next/navigation";

export default async function ModulePage({ params }: { params: Promise<{ module: string }> }) {
  const { module } = await params;
  const definition = moduleDefinitions[module];
  if (!definition) notFound();

  try {
    const user = await currentTenantUser();
    let hydratedDefinition = definition;
    if (module === "ocorrencias") {
      try {
        const response = await tenantFetch<{ items: Array<{ id: number; legacy_id: number; title: string; description: string | null; category: string; owner: string; status: string; updated_at: string }> }>("/occurrences?page_size=100");
        if (response.items.length) {
          hydratedDefinition = {
            ...definition,
            records: response.items.map((item) => ({
              id: item.id,
              title: item.title,
              description: item.description ?? undefined,
              category: item.category,
              owner: item.owner,
              status: item.status,
              updatedAt: new Intl.DateTimeFormat("pt-BR").format(new Date(item.updated_at)),
            })),
          };
        }
      } catch (error) {
        if (error instanceof Error && error.message === "unauthorized") throw error;
      }
    }
    return <OperationalModule definition={hydratedDefinition} user={user} />;
  } catch (error) {
    if (error instanceof Error && error.message === "unauthorized") redirect("/login");
    throw error;
  }
}
