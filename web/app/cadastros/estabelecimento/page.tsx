import { AppLayout } from "@/components/app-layout";
import { OperationalModule } from "@/components/operational-module";
import { currentTenantUser } from "@/lib/api";
import { moduleDefinitions } from "@/lib/module-definitions";
import { redirect } from "next/navigation";

export default async function EstabelecimentoPage() {
  const definition = moduleDefinitions["cadastros/estabelecimento"];

  try {
    const user = await currentTenantUser();
    return (
      <AppLayout user={user}>
        <OperationalModule definition={definition} user={user} />
      </AppLayout>
    );
  } catch (error) {
    if (error instanceof Error && error.message === "unauthorized")
      redirect("/login");
    throw error;
  }
}
