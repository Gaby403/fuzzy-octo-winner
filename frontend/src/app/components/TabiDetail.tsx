import { TabiStroke } from "./TabiStroke";
export function TabiDetail({ corner = "bottom-right", size = "clamp(120px, 12vw, 180px)", opacity = 0.2, color = "#F20C25", }: {
    corner?: "bottom-right" | "top-right" | "bottom-left";
    size?: string;
    opacity?: number;
    color?: string;
}) {
    const pos: React.CSSProperties = corner === "top-right"
        ? { top: "12%", right: "4%" }
        : corner === "bottom-left"
            ? { bottom: "6%", left: "4%" }
            : { bottom: "6%", right: "4%" };
    return (<div aria-hidden="true" className="hidden md:block pointer-events-none" style={{ position: "absolute", width: size, opacity, zIndex: 0, ...pos }}>
      <TabiStroke width="100%" color={color} strokeWidth={1.4} duration={2.6}/>
    </div>);
}
