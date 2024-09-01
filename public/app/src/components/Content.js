import React from "react";
import parse, { domToReact } from "html-react-parser";
import styled from "@emotion/styled";
import { Box } from "@chakra-ui/react";
import Code from "./Code";
// import ulIcon from "../images/ul-icon.png";

// https://www.npmjs.com/package/@wordpress/block-library
// import "@wordpress/block-library/build-style/style.css"
// import "@wordpress/block-library/build-style/theme.css"

const replaceCode = (node) => {
  if (node.name === "pre") {
    return (
      node.children.length > 0 && (
        <Code language={getLanguage(node)}>{domToReact(getCode(node))}</Code>
      )
    );
  }
};

const getLanguage = (node) => {
  if (node.attribs["data-lang"] != null) {
    return node.attribs["data-lang"];
  }
  return null;
};

const getCode = (node) => {
  if (node.children.length > 0 && node.children[0].name === "code") {
    return node.children[0].children;
  } else {
    return node.children;
  }
};

const Content = ({ content, colorScheme, ...props }) => {
  return <Box {...props}>{parse(content, { replace: replaceCode })}</Box>;
};

const StyledContent = styled(Content)`
  h1,
  h2,
  h3,
  h4,
  h5,
  h6 {
    font-weight: 700;
    margin: 2.5rem 0 1rem;
  }

  h1 {
    font-size: 38px;
  }

  h2 {
    font-size: 28px;
  }

  h3 {
    font-size: 24px;
  }

  h4 {
    font-size: 20px;
  }

  h5 {
    font-size: 16px;
  }

  h6 {
    font-size: 16px;
    font-style: italic;
  }

  p {
    font-size: 16px;
    line-height: 1.625;
    margin: 1rem 0;
  }

  a {
    color: ${({ colorScheme, theme }) =>
      colorScheme ? theme.colors[colorScheme]["300"] : "inherit"};
    text-decoration: underline;
  }

  strong {
    font-weight: 700;
  }

  em {
    font-style: italic;
  }

  sup {
    font-size: 10px;
    position: relative;
    top: -7px;
    margin: 0 2px;

    a {
      text-decoration: none;
    }
  }

  code {
    font-family: ${({ theme }) => (theme ? theme.fonts.mono : "inherit")};
    display: inline-block;
    padding: 0 10px;
    background-color: #f3efec;
  }

  pre {
    position: relative;

    code {
      font-size: 14px;
      position: relative;
      display: block;
      margin: 1.5rem 0;
      padding: 40px 25px 20px;
      background-color: #f3efec;

      &[class*="language-"]:before {
        color: ${({ theme }) => theme.colors.gray["900"]};
        font-weight: 500;
        position: absolute;
        top: 0;
        left: 20px;
        padding: 2px 10px;
        background-color: ${({ colorScheme, theme }) =>
          colorScheme
            ? theme.colors[colorScheme]["500"]
            : theme.colors.gray["500"]};
      }

      &.language-javascript:before {
        content: "JS";
      }
    }

    // "code.language-javascript:before": {
    //   content: '"JS"',
    // },

    button {
      font-size: 14px;
      font-weight: 400;
      position: absolute;
      top: 5px;
      right: 5px;
      display: block;
      padding: 0 8px;
      border-radius: 25px;
      background-color: #ffffff;
    }
  }

  ol,
  ul {
    font-size: 16px;
    line-height: 1.625;
    list-style: none;
    margin: 1rem 0;
    padding-left: 40px;

    li {
      position: relative;
    }

    li:before {
      color: ${({ colorScheme, theme }) =>
        colorScheme
          ? theme.colors[colorScheme]["500"]
          : theme.colors.gray["500"]};
      position: absolute;
      top: 0;
      left: -22px;
    }
  }

  ol {
    counter-reset: item;

    li:before {
      content: counter(item) ".";
      counter-increment: item;
      font-weight: 600;
    }
  }

  ul {
    li:before {
      content: "•";
      font-size: 20px;
      line-height: 0.7;
    }
  }

  blockquote {
    font-size: 28px;
    font-weight: 400;
    margin: 1.5rem 0;
    padding: 15px 25px;
    border: 2px solid;
    border-left: 10px solid;
    border-color: ${({ colorScheme, theme }) =>
      colorScheme
        ? theme.colors[colorScheme]["200"]
        : theme.colors.gray["200"]};

    p {
      font-size: inherit;
    }
  }
`;

export default StyledContent;
