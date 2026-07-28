import { useRef } from "react";
import { m, useInView } from "motion/react";
export function TextReveal({ text, delay = 0, stagger = 0.018, as: Tag = "p", style, className, }: {
    text: string;
    delay?: number;
    stagger?: number;
    as?: "p" | "h2" | "h3" | "span";
    style?: React.CSSProperties;
    className?: string;
}) {
    const ref = useRef<HTMLDivElement>(null);
    const inView = useInView(ref, { once: true, margin: "-12%" });
    const reduced = typeof window !== "undefined" && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const words = text.split(" ");
    return (<Tag ref={ref as never} className={className} style={style}>
      {words.map((w, i) => (<span key={`${w}-${i}`} style={{ display: "inline-block", overflow: "hidden", verticalAlign: "top" }}>
          <m.span style={{ display: "inline-block", willChange: "transform" }} initial={reduced ? false : { y: "110%" }} animate={inView || reduced ? { y: "0%" } : undefined} transition={{ duration: 0.7, delay: delay + i * stagger, ease: [0.16, 1, 0.3, 1] }}>
            {w}
          </m.span>
          {i < words.length - 1 ? " " : ""}
        </span>))}
    </Tag>);
}
