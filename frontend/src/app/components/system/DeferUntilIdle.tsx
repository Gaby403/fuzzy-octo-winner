import { useEffect, useState } from "react";
export function DeferUntilIdle({ children, timeout = 2000 }: {
    children: React.ReactNode;
    timeout?: number;
}) {
    const [pronto, setPronto] = useState(false);
    useEffect(() => {
        let cancelado = false;
        const liberar = () => { if (!cancelado)
            setPronto(true); };
        const ric = (window as unknown as {
            requestIdleCallback?: (cb: () => void, o?: {
                timeout: number;
            }) => number;
        }).requestIdleCallback;
        const raf = requestAnimationFrame(() => {
            if (ric)
                ric(liberar, { timeout });
            else
                setTimeout(liberar, 200);
        });
        return () => { cancelado = true; cancelAnimationFrame(raf); };
    }, [timeout]);
    return pronto ? <>{children}</> : null;
}
