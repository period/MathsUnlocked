export default function({ $axios, redirect }) {
    $axios.interceptors.response.use(function (response) {
        return response;
    }, function (error) {
        if(error.response.status == 429) return $nuxt.$toastr('error', "Too many requests - try again in a few minutes", "Error");
        $nuxt.$toastr('error', error.response.data.message, "Error");
        if(error.response.status == 403 || error.response.status == 401) {
            localStorage.setItem('authorization', '')
            $nuxt.$router.push('/login')
        }
        return Promise.reject(error);
      });

  $axios.interceptors.request.use((request) => requestHandler(request))

  const isHandlerEnabled = (config = {}) => {
    return config.hasOwnProperty('handlerEnabled') && !config.handlerEnabled
      ? false
      : true
  }

  const requestHandler = (request) => {
    if (isHandlerEnabled(request)) {
        request.headers['Authorization'] = localStorage.getItem("authorization"); 
    }
    return request
  }
}