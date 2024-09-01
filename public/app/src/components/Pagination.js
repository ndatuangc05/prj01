import React, { useState } from "react";
import {
  Stack,
  // Link,
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
} from "@chakra-ui/react";
// import { ChevronLeftIcon, ChevronRightIcon } from "@chakra-ui/icons";

const DISPLAY = 999;

function Pagination({ pages, currentPage, onChange, ...props }) {
  const [range] = useState([0, DISPLAY]);

  const handleChange = (incomingPage) => {
    if (onChange) {
      onChange(incomingPage);
    }
  };

  if (!pages || Array.from({ length: pages }).length <= 1) {
    return null;
  }

  return (
    <Stack
      color="gray.600"
      fontSize="sm"
      fontWeight="semibold"
      letterSpacing="wide"
      textTransform="uppercase"
      direction="row"
      justifyContent="center"
      alignItems="center"
      spacing={10}
      my={6}
      {...props}
    >
      {/* <Link
        display="inline-flex"
        alignItems="center"
        onClick={() => handleChange(currentPage - 1)}
      >
        <ChevronLeftIcon w={4} h={4} mr={1} /> Prev
      </Link> */}
      <Breadcrumb
        separator=" | "
        spacing={3}
        textAlign="center"
        display={["none", "none", "block"]}
      >
        {Array.from({ length: pages }, (_, i) => i + 1)
          .slice(range[0], range[1])
          .map((page) => {
            console.log(currentPage - 1);
            return (
              <BreadcrumbItem
                key={page}
                isCurrentPage={page === currentPage}
                my="0 !important"
              >
                <BreadcrumbLink
                  color={page === currentPage ? "black" : "inherit"}
                  fontWeight={page === currentPage ? "bold" : "normal"}
                  onClick={() => handleChange(page)}
                >
                  {page}
                </BreadcrumbLink>
              </BreadcrumbItem>
            );
          })}
      </Breadcrumb>
      {/* <Link
        display="inline-flex"
        alignItems="center"
        onClick={() => handleChange(currentPage + 1)}
      >
        Next <ChevronRightIcon w={4} h={4} mr={1} />
      </Link> */}
    </Stack>
  );
}

Pagination.defaultProps = {
  pages: 0,
  currentPage: 1,
};

export default Pagination;
