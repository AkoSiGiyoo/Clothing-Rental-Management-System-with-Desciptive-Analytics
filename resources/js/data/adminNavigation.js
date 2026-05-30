export function buildAdminNavigation(current = "dashboard") {
    return [
        { label: "Dashboard", icon: "fa-gauge-high", href: "/", active: current === "dashboard" },
        { label: "Manage Clothing", icon: "fa-shirt", href: "/clothing", active: current === "clothing" },
        { label: "Clothing Categories", icon: "fa-tags", href: "/clothing-categories", active: current === "categories" },
        { label: "Inventory", icon: "fa-boxes-stacked", href: "#", active: false },
        { label: "Rental", icon: "fa-file-lines", href: "#", active: false },
        { label: "Customer", icon: "fa-users", href: "#", active: false },
        { label: "Reports & Analytics", icon: "fa-chart-line", href: "#", active: false },
        { label: "Payment", icon: "fa-credit-card", href: "#", active: false },
    ];
}
