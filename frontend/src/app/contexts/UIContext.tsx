import { createContext, useCallback, useContext, useState, ReactNode } from "react";
interface UIContextValue {
    menuOpen: boolean;
    openMenu: () => void;
    closeMenu: () => void;
    toggleMenu: () => void;
}
const UIContext = createContext<UIContextValue>({
    menuOpen: false,
    openMenu: () => { },
    closeMenu: () => { },
    toggleMenu: () => { },
});
export function UIProvider({ children }: {
    children: ReactNode;
}) {
    const [menuOpen, setMenuOpen] = useState(false);
    const openMenu = useCallback(() => setMenuOpen(true), []);
    const closeMenu = useCallback(() => setMenuOpen(false), []);
    const toggleMenu = useCallback(() => setMenuOpen(o => !o), []);
    return <UIContext.Provider value={{ menuOpen, openMenu, closeMenu, toggleMenu }}>{children}</UIContext.Provider>;
}
export function useUI() {
    return useContext(UIContext);
}
