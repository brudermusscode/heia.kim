import * as Frontend from "../frontend";

document.addEventListener("scroll", async (e) => {
  let _container = document.body.querySelector('[scroll="infinite"]');
  let real_bodyScrollHeight = document.body.scrollHeight - window.innerHeight;
  let formdata;
  let formatted_data;

  if (
    !_container ||
    __infinite_scroll.reached_end ||
    !window.location.pathname.includes("artist/") ||
    __page.is_loading
  )
    return;

  formdata = new FormData(
    document.body.querySelector('[data-form="artists:scroll"]')
  );
  formatted_data = new URLSearchParams(formdata);

  if (
    window.scrollY >= real_bodyScrollHeight - __infinite_scroll.page_offset &&
    !(__infinite_scroll.reached_end || __infinite_scroll.reached_full_end)
  ) {
    __infinite_scroll.reached_end = true;
    await load_beatmaps(formatted_data, _container);
    __infinite_scroll.reached_end = false;
  }
});

export const load_beatmaps = async (data, append) => {
  let offset = document.body.querySelector(
    '[data-react="infinite-scroll-offset"]'
  );
  let offset_number = parseInt(offset.value);
  let loader = document.body.querySelector('[data-react="scroll:reached-end"]');
  let page_end_bird = document.body.querySelector("[page-end-bird]");

  if (loader) {
    loader.setAttribute("show-loader", "");
    loader.removeAttribute("style", "");
  }

  if (page_end_bird) page_end_bird.style.display = "none";

  await axios.get("/artist/set/fetch?" + data).then(async (data) => {
    if (data.data.status && !data.data.end) {
      await append.insertAdjacentHTML("beforeend", data.data.data);

      if (offset)
        offset.value = offset_number + parseInt(__infinite_scroll.limit);

      Frontend.reload_images();
    } else {
      console.log("Reached full end! No more maps to fetch.");
      if (loader) loader.remove();
      if (page_end_bird) page_end_bird.removeAttribute("style");
      __infinite_scroll.reached_full_end = true;
    }
  });
};
