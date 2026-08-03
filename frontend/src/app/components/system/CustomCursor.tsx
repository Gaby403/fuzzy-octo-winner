import { useEffect, useState } from "react";
import { m, useMotionValue, useSpring, AnimatePresence } from "motion/react";
import { TabiMark } from "../TabiMark";
const RED_INK = "#FF3547";
export function CustomCursor() {
    const [enabled, setEnabled] = useState(false);
    const [hovering, setHovering] = useState(false);
    const [label, setLabel] = useState("");
    const x = useMotionValue(-100);
    const y = useMotionValue(-100);
    const ringX = useSpring(x, { stiffness: 350, damping: 30, mass: 0.6 });
    const ringY = useSpring(y, { stiffness: 350, damping: 30, mass: 0.6 });
    useEffect(() => {
        const finePointer = window.matchMedia("(hover: hover) and (pointer: fine)").matches;
        const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        if (!finePointer || reduced)
            return;
        setEnabled(true);
        document.documentElement.classList.add("has-custom-cursor");
        const move = (e: MouseEvent) => {
            x.set(e.clientX);
            y.set(e.clientY);
        };
        const over = (e: MouseEvent) => {
            const el = (e.target as HTMLElement | null)?.closest("a, button, [data-cursor]") as HTMLElement | null;
            setHovering(!!el);
            setLabel(el?.getAttribute("data-cursor-label") || "");
        };
        window.addEventListener("mousemove", move, { passive: true });
        window.addEventListener("mouseover", over, { passive: true });
        return () => {
            window.removeEventListener("mousemove", move);
            window.removeEventListener("mouseover", over);
            document.documentElement.classList.remove("has-custom-cursor");
        };
    }, [x, y]);
    if (!enabled)
        return null;
    return (<>
      
      <m.div aria-hidden style={{ position: "fixed", top: 0, left: 0, x, y, zIndex: 10000, pointerEvents: "none" }}>
        <div style={{ width: 6, height: 6, marginLeft: -3, marginTop: -3, borderRadius: "50%", background: RED_INK }}/>
      </m.div>

      
      <m.div aria-hidden style={{ position: "fixed", top: 0, left: 0, x: ringX, y: ringY, zIndex: 9999, pointerEvents: "none", mixBlendMode: "difference" }}>
        <m.div style={{ width: 40, height: 40, marginLeft: -20, marginTop: -20, borderRadius: "50%", border: "1.5px solid #fff", display: "flex", alignItems: "center", justifyContent: "center" }} animate={{ scale: hovering ? 1.6 : 1 }} transition={{ type: "spring", stiffness: 300, damping: 22 }}>
          <AnimatePresence>
            {label && (<m.span initial={{ opacity: 0, scale: 0.5 }} animate={{ opacity: 1, scale: 1 }} exit={{ opacity: 0, scale: 0.5 }} style={{ fontSize: 8, fontWeight: 700, letterSpacing: "0.08em", textTransform: "uppercase", color: "#fff", whiteSpace: "nowrap" }}>
                {label}
              </m.span>)}
          </AnimatePresence>
        </m.div>
      </m.div>

      {/* Fora do anel, senão o glifo tapa justamente o rótulo que a pessoa vai
          clicar. Sombra em vez de mix-blend: sobrevive ao vermelho do botão e
          ao cinza claro do hero sem virar ciano. */}
      <m.div aria-hidden style={{ position: "fixed", top: 0, left: 0, x: ringX, y: ringY, zIndex: 9998, pointerEvents: "none" }}>
        <AnimatePresence>
          {hovering && !label && (<m.div initial={{ opacity: 0, scale: 0.4, rotate: -22 }} animate={{ opacity: 1, scale: 1, rotate: 0 }} exit={{ opacity: 0, scale: 0.4, rotate: 22 }} transition={{ duration: 0.25, ease: [0.16, 1, 0.3, 1] }} style={{ marginLeft: 24, marginTop: 20, filter: "drop-shadow(0 0 1.5px rgba(0,0,0,0.9)) drop-shadow(0 1px 3px rgba(0,0,0,0.55))" }}>
              <TabiMark width={15} color="#fff"/>
            </m.div>)}
        </AnimatePresence>
      </m.div>
    </>);
}
