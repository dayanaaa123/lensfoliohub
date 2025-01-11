function toggleMenu() {
    const navbar = document.getElementById("navbar"); 
    if (navbar.classList.contains("show")) {
        navbar.classList.remove("show"); 
    } else {
        navbar.classList.add("show"); 
    }
}