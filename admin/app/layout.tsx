import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "Registro Admin",
  description: "Administração SaaS do Registro",
};

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return <html lang="pt-BR"><body>{children}</body></html>;
}
