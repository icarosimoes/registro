import { AppLayout } from "@/components/app-layout";
import { currentTenantUser } from "@/lib/api";
import { redirect } from "next/navigation";
import { SettingsTabs } from "./tabs";

export default async function ConfiguracoesPage({
  searchParams,
}: {
  searchParams: Promise<Record<string, string | undefined>>;
}) {
  const query = await searchParams;
  const tab = query.tab ?? "estabelecimento";

  try {
    const user = await currentTenantUser();
    return (
      <AppLayout user={user}>
        <header className="module-heading">
          <div>
            <p className="eyebrow">Configurações</p>
            <h1>Configurações</h1>
            <p>Gerencie os dados do estabelecimento, integrações e sua conta pessoal.</p>
          </div>
        </header>
        <SettingsTabs activeTab={tab} user={user} />
      </AppLayout>
    );
  } catch (error) {
    if (error instanceof Error && error.message === "unauthorized")
      redirect("/login");
    throw error;
  }
}
