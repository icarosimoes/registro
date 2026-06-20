"use client";

import { useEffect, useState } from "react";

function computeSla(deadline: string) {
  const now = Date.now();
  const end = new Date(deadline).getTime();
  const diff = end - now;
  const expired = diff <= 0;
  const absDiff = Math.abs(diff);
  const hours = Math.floor(absDiff / 3_600_000);
  const minutes = Math.floor((absDiff % 3_600_000) / 60_000);
  const warning = !expired && diff < 6 * 3_600_000;
  return { expired, warning, hours, minutes };
}

export function SlaIndicator({ deadline }: { deadline: string }) {
  const [sla, setSla] = useState(() => computeSla(deadline));

  useEffect(() => {
    setSla(computeSla(deadline));
    const id = setInterval(() => setSla(computeSla(deadline)), 60_000);
    return () => clearInterval(id);
  }, [deadline]);

  if (sla.expired) {
    return <span className="sla-badge sla-critical">Vencido há {sla.hours}h{sla.minutes > 0 ? ` ${sla.minutes}min` : ""}</span>;
  }
  return <span className={`sla-badge${sla.warning ? " sla-warning" : ""}`}>{sla.hours}h {sla.minutes}min restantes</span>;
}
