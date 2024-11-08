function togglePanel(panelId) {
    const panels = ["registroPanel", "materiasPanel", "asistenciasPanel", "notasPanel"];
    panels.forEach(id => {
        document.getElementById(id).style.display = (id === panelId && document.getElementById(id).style.display === "none") ? "block" : "none";
    });
}

document.getElementById("togglePanel").onclick = () => togglePanel("registroPanel");
document.getElementById("toggleMaterias").onclick = () => togglePanel("materiasPanel");
document.getElementById("toggleAsistencias").onclick = () => togglePanel("asistenciasPanel");
document.getElementById("toggleNotas").onclick = () => togglePanel("notasPanel");
