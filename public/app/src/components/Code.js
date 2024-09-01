import React from "react";
import { Prism as SyntaxHighlighter } from "react-syntax-highlighter";
import { nord as style } from "react-syntax-highlighter/dist/esm/styles/prism";

const Code = ({ language, children }) => (
  <SyntaxHighlighter style={style} language={language}>
    {children}
  </SyntaxHighlighter>
);

export default Code;
