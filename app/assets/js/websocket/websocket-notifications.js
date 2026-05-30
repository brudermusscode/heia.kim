// web socket url
let socketIO = io("http://localhost:3000");

// new notification pushed to socket
socketIO.on("newNotification", function (data) {

  console.log(data);

  let notif_counter = document.querySelector("[menu-count=notification]");
  let notif_counter_html = notif_counter.querySelector("p");
  let notif_counter_count = parseInt(notif_counter_html.innerHTML);

  notif_counter_count = notif_counter_count + 1;
  notif_counter_html.innerHTML = notif_counter_count;

  console.log(notif_counter_count);

  notif_counter.setAttribute("active", true);
});
