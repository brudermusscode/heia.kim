/**
 *
 * Serialize a query string into a friendly url
 * @param query: string
 * @return: object
 */
export const friendly_url = (url) => {
  if (!url) return null;

  let data = new FormData();
  let url_split = url.split("?");
  let url_page = url_split[0];
  let url_search = url_split[1] ? url_split[1] : null;
  let url_params = new URLSearchParams(url_search);
  let friendly_url = url_page;

  if (url_search) {
    url_params.forEach((param, key) => {
      friendly_url += "/" + param;
      data.append(key, param);
    });
  }

  return {
    url: friendly_url,
    data: data,
  };
};
