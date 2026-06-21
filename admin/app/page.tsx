import { redirect } from "next/navigation";
import { getPlatformToken } from "@/lib/api";

export default async function Home() {
  const token = await getPlatformToken();
  redirect(token ? "/dashboard" : "/login");
}
