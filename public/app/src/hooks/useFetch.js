// https://www.robinwieruch.de/react-hooks-fetch-data

import { useState } from "react";
import axios from "axios";
import useSWR from "swr";
import qs from "query-string";

// reject non-json response types
// axios.interceptors.response.use(
//   (res) => {
//     return res.headers["content-type"].includes("application/json")
//       ? res
//       : Promise.reject(res);
//   },
//   (err) => Promise.reject(err)
// );

const fetcher = (url, query) => {
  if (query) {
    url = url + query;
  }

  return axios(url).then((res) => res.data);
};

const createQueryString = (params) => {
  let query = Object.keys(params)
    .map((key) =>
      qs.stringify({ [key]: params[key] }, { arrayFormat: "comma" })
    )
    .join("&");

  if (query) {
    query = "?".concat(query);
  }

  return query;
};

export default function useFetch(initialUrl) {
  const [url, setUrl] = useState(initialUrl);
  const [query, setQuery] = useState(null);
  const { data, error, isValidating, mutate } = useSWR([url, query], fetcher);

  const refetch = (refetchUrl, refetchParams) => {
    const queryString = createQueryString(refetchParams);

    setUrl(refetchUrl);
    setQuery(queryString);
  };

  return [
    {
      loading: !error && !data,
      error,
      data,
      isValidating,
      mutate,
    },
    refetch,
  ];
}

// const fetchReducer = (state, action) => {
//   switch (action.type) {
//     case "FETCH_INIT":
//       return {
//         ...state,
//         loading: true,
//         error: false,
//       };
//     case "FETCH_SUCCESS":
//       return {
//         ...state,
//         loading: false,
//         error: false,
//         data: action.payload,
//       };
//     case "FETCH_FAILURE":
//       return {
//         ...state,
//         loading: false,
//         error: true,
//       };
//     default:
//       throw new Error();
//   }
// };

// export function useLazyFetch(initialUrl, initialData) {
//   const [state, dispatch] = useReducer(fetchReducer, {
//     loading: false,
//     error: false,
//     data: initialData,
//   });

//   const fetchData = async (url = initialUrl) => {
//     dispatch({ type: "FETCH_INIT" });

//     try {
//       const response = await axios(url);

//       dispatch({ type: "FETCH_SUCCESS", payload: response.data });

//       return response.data;
//     } catch (error) {
//       dispatch({ type: "FETCH_FAILURE" });

//       return new Error(`Failed to get requested data...`);
//     }
//   };

//   return [fetchData, state];
// }

// export default function useFetch(initialUrl, initialData) {
//   const [url, setUrl] = useState(initialUrl);

//   const [state, dispatch] = useReducer(fetchReducer, {
//     loading: false,
//     error: false,
//     data: initialData,
//   });

//   useEffect(() => {
//     let didCancel = false;

//     const fetchData = async () => {
//       dispatch({ type: "FETCH_INIT" });

//       try {
//         const response = await axios(url);

//         if (!didCancel) {
//           dispatch({ type: "FETCH_SUCCESS", payload: response.data });
//         }
//       } catch (error) {
//         // if (error.response) {
//         //   // Request made and server responded
//         //   console.log(error.response.data)
//         //   console.log(error.response.status)
//         //   console.log(error.response.headers)
//         // } else if (error.request) {
//         //   // The request was made but no response was received
//         //   console.log(error.request)
//         // } else {
//         //   // Something happened in setting up the request that triggered an Error
//         //   console.log('Error', error.message)
//         // }

//         if (!didCancel) {
//           dispatch({ type: "FETCH_FAILURE" });
//         }
//       }
//     };

//     fetchData();

//     return () => {
//       didCancel = true;
//     };
//   }, [url]);

//   return [state, setUrl];
// }
