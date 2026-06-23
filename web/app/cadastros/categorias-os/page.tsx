import { AppLayout } from "@/components/app-layout";
import { currentTenantUser } from "@/lib/api";
import { redirect } from "next/navigation";
import { CategoryManager } from "./manager";

export default async function CategoriasOSPage() {
  try {
    const user = await currentTenantUser();
    return (
      <AppLayout user={user}>
        <header className="module-heading">
          <div>
            <p className="eyebrow">Cadastros</p>
            <h1>Categorias de OS</h1>
            <p>Gerencie as categorias disponíveis para Ordens de Serviço.</p>
          </div>
        </header>
        <CategoryManager />
      </AppLayout>
    );
  } catch (error) {
    if (error instanceof Error && error.message === "unauthorized")
      redirect("/login");
    throw error;
  }
}
