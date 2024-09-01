import React from "react";
import { BrowserRouter, Switch, Route } from "react-router-dom";
import { Global, css } from "@emotion/react";
import { ChakraProvider, ColorModeScript } from "@chakra-ui/react";
import { generateTheme } from "./styles/theme";
import useFetch from "./hooks/useFetch";
import Archive from "./pages/Archive";
import Single from "./pages/Single";
import GlobalStyles from "./styles/GlobalStyles";

function App({ config }) {
  const settingsUrl = `${config.apiUrl}/settings`;
  const [{ loading, error, data: settings }] = useFetch(settingsUrl);

  if ((loading && !settings) || error) return null;

  const theme = generateTheme({
    theme: settings.theme_json,
    fonts: {
      body: settings.theme_body_font_family,
      heading: settings.theme_heading_font_family,
      mono: settings.theme_mono_font_family,
    },
  });

  return (
    <div id="resources-wp">
      <ChakraProvider resetCSS={true} theme={theme}>
        <ColorModeScript initialColorMode={theme.config.initialColorMode} />
        <GlobalStyles />
        <Global
          styles={css({
            "#resources-wp": {
              backgroundColor: settings.theme_background_color
                ? settings.theme_background_color
                : null,
            },
          })}
        />
        <BrowserRouter>
          <Switch>
            <Route
              path={`/${config.slug}/:slug/`}
              render={(props) => (
                <Single {...props} config={config} settings={settings} />
              )}
            />
            <Route
              path={`/${config.slug}/`}
              component={(props) => (
                <Archive {...props} config={config} settings={settings} />
              )}
            />
          </Switch>
        </BrowserRouter>
      </ChakraProvider>
    </div>
  );
}

export default App;
