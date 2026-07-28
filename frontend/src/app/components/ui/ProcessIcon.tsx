export function ProcessIcon({ name, size = 28, color = "currentColor" }: {
    name: string;
    size?: number;
    color?: string;
}) {
    const common = { width: size, height: size, viewBox: "0 0 24 24", fill: "none", stroke: color, strokeWidth: 1.6, strokeLinecap: "round" as const, strokeLinejoin: "round" as const, "aria-hidden": true };
    switch (name) {
        case "diagnostico":
            return (<svg {...common}><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.5" y2="16.5"/></svg>);
        case "narrativa":
            return (<svg {...common}><path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>);
        case "design":
            return (<svg {...common}><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.586 7.586"/><circle cx="11" cy="11" r="2"/></svg>);
        case "desenvolvimento":
            return (<svg {...common}><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>);
        default:
            return (<svg {...common}><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>);
    }
}
