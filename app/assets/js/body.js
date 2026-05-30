export const toggle = async () => {
  let attr = document.body.getAttribute("toggled");

  if (attr == "true") {
    document.body.setAttribute("toggled", false);
  } else {
    document.body.setAttribute("toggled", true);
  }
};
