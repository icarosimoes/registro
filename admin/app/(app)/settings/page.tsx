import { Settings } from "lucide-react";

export default function SettingsPage() {
  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-bold text-gray-900">Configurações</h1>
        <p className="text-sm text-gray-500">Preferências da plataforma.</p>
      </header>

      <div className="rounded-xl border border-gray-100 bg-white shadow-sm p-12 text-center">
        <Settings className="h-12 w-12 text-gray-300 mx-auto mb-4" />
        <p className="text-gray-500">Configurações da plataforma em breve.</p>
      </div>
    </div>
  );
}
