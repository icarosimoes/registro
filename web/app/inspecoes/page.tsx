import { AppLayout } from "@/components/app-layout";
import { ChecklistManager } from "@/components/checklist-manager";
import { InspectionViewer, type InspectionRecord } from "@/components/inspection-viewer";
import { currentTenantUser, tenantFetch } from "@/lib/api";
import { redirect } from "next/navigation";
import { InspecoesTabBar } from "./tabs";

export default async function InspecoesPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | undefined>>;
}) {
  const query = await searchParams;
  const tab = query.tab ?? "inspecoes";

  try {
    const user = await currentTenantUser();

    if (tab === "checklists") {
      type ChecklistItem = {
        id: number; name: string; description: string | null;
        recurrence: string; category: string | null;
        assigned_user_name: string | null; active: boolean;
        next_due: string | null; item_count: number;
        created_at: string; updated_at: string;
      };
      type ChecklistPage = { items: ChecklistItem[]; total: number; page: number; page_size: number };

      let templates: ChecklistItem[] = [];
      try {
        const data = await tenantFetch<ChecklistPage>("/checklists/templates?page=1&page_size=100");
        templates = data.items;
      } catch (error) {
        if (error instanceof Error && error.message === "unauthorized") throw error;
      }

      return (
        <AppLayout user={user}>
          <InspecoesTabBar activeTab="checklists" />
          <ChecklistManager templates={templates.map((t) => ({
            id: t.id,
            name: t.name,
            description: t.description,
            recurrence: t.recurrence,
            category: t.category,
            assigned_user_name: t.assigned_user_name,
            active: t.active,
            item_count: t.item_count,
          }))} />
        </AppLayout>
      );
    }

    type ModuleRecordItem = {
      id: number; title: string; description: string | null;
      category: string | null; owner: string; status: string;
      payload: Record<string, unknown> | null; updated_at: string;
    };
    type ModuleRecordPage = { items: ModuleRecordItem[]; total: number; page: number; page_size: number };

    const pg = Math.max(1, parseInt(query.page ?? "1", 10) || 1);
    const search = query.search ?? "";
    let records: InspectionRecord[] = [];
    let total = 0;

    try {
      const searchParam = search ? `&search=${encodeURIComponent(search)}` : "";
      const data = await tenantFetch<ModuleRecordPage>(`/modules/inspecoes?page=${pg}&page_size=20${searchParam}`);
      total = data.total;
      records = data.items.map((item) => ({
        id: item.id,
        title: item.title,
        description: item.description,
        category: item.category,
        owner: item.owner,
        status: item.status,
        payload: item.payload as InspectionRecord["payload"],
        updated_at: item.updated_at,
      }));
    } catch (error) {
      if (error instanceof Error && error.message === "unauthorized") throw error;
    }

    return (
      <AppLayout user={user}>
        <InspecoesTabBar activeTab="inspecoes" />
        <InspectionViewer records={records} total={total} page={pg} pageSize={20} search={search} />
      </AppLayout>
    );
  } catch (error) {
    if (error instanceof Error && error.message === "unauthorized")
      redirect("/login");
    throw error;
  }
}
