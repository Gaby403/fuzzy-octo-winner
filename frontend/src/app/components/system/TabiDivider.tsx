import { useRef } from "react";
import { m, useInView } from "motion/react";
import { TabiMark } from "../TabiMark";

const EASE: [number, number, number, number] = [0.16, 1, 0.3, 1];

/**
 * Divisor de seção: dois traços que abrem a partir do centro e deixam o 旅
 * no meio. Serve de respiro entre blocos e repete a assinatura sem pesar.
 */
export function TabiDivider({ cor = "rgba(239,239,239,0.14)", marca = "rgba(239,239,239,0.22)", margem = "clamp(56px,8vw,110px)" }: {
    cor?: string;
    marca?: string;
    margem?: string;
}) {
    const ref = useRef<HTMLDivElement>(null);
    const visivel = useInView(ref, { once: true, margin: "-12% 0px" });
    const traco = (origem: "right" | "left") => (<m.div style={{ flex: 1, height: 1, background: cor, transformOrigin: origem }} initial={{ scaleX: 0 }} animate={visivel ? { scaleX: 1 } : {}} transition={{ duration: 1.1, ease: EASE }}/>);

    return (<div ref={ref} aria-hidden="true" style={{
            display: "flex", alignItems: "center", gap: "clamp(16px,3vw,30px)",
            margin: `${margem} auto`, maxWidth: 1180, paddingInline: "clamp(20px,5vw,32px)",
            pointerEvents: "none",
        }}>
      {traco("right")}
      <m.div initial={{ opacity: 0, scale: 0.6, rotate: -18 }} animate={visivel ? { opacity: 1, scale: 1, rotate: 0 } : {}} transition={{ duration: 0.75, delay: 0.18, ease: EASE }} style={{ width: "clamp(20px,2.4vw,30px)", flexShrink: 0 }}>
        <TabiMark width="100%" color={marca}/>
      </m.div>
      {traco("left")}
    </div>);
}
