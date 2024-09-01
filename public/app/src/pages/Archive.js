import React, { useRef, useState, useEffect } from "react";
import { Link as RouterLink } from "react-router-dom";
import { isEmpty } from "lodash";
import {
  Box,
  Container,
  Grid,
  Flex,
  Text,
  Stack,
  Checkbox,
  Input,
  Button,
} from "@chakra-ui/react";
import Message from "../components/Message";
import Hero from "../components/Hero";
import Card, { SkeletonCard } from "../components/Card";
import { getDateStatus } from "../utils/helpers";
import useFetch from "../hooks/useFetch";
import Pagination from "../components/Pagination";
import { colorSchemeMap } from "../utils/helpers";

function Archive({ config, settings }) {
  const topRef = useRef(null);
  const [categories, setCategories] = useState([]);
  const [search, setSearch] = useState("");
  const [currentPage, setCurrentPage] = useState(1);

  const resourcesUrl = `${config.apiUrl}/resources`;
  const categoriesUrl = `${config.apiUrl}/categories`;

  const [{ loading, error, data }, refetch] = useFetch(resourcesUrl);

  const [{ data: categoriesData }] = useFetch(categoriesUrl);

  useEffect(() => {
    refetch(resourcesUrl, { categories, search, paged: currentPage });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [categories, search, currentPage]);

  const handleFilter = (e) => {
    if (categories.includes(e.target.value)) {
      setCategories(
        categories.filter((category) => category !== e.target.value)
      );
    } else {
      setCategories([...categories, e.target.value]);
    }
  };

  const handleSearch = (e) => {
    e.preventDefault();
    setSearch(e.target.search.value);
  };

  const handlePageChange = (page) => {
    setCurrentPage(page);

    if (topRef.current) {
      window.scrollTo({
        top: topRef.current.offsetTop,
        left: 0,
        behavior: "smooth",
      });
    }
  };

  if (error) return <Message message="Error... please refresh your browser." />;

  return (
    <Box className="resources-wp-archive">
      {settings && settings.archive_header_enabled && (
        <Hero
          className="resources-wp-archive__hero"
          kicker={settings.archive_header_kicker}
          title={settings.archive_header_title}
          content={settings.archive_header_content}
          color={settings.archive_header_font_color}
          bgColor={settings.archive_header_background_color}
          bgImage={settings.archive_header_background_image}
        />
      )}

      <Container maxW="1440px" py={20}>
        <Flex
          className="resources-wp-archive__filters"
          flexWrap="wrap"
          justifyContent="space-between"
          alignItems="center"
          mb={6}
        >
          <Stack
            className="resources-wp-archive__filter"
            flex="1 1 60%"
            direction="row"
            spacing={4}
            wrap="wrap"
            alignItems="center"
            my={2}
          >
            {!isEmpty(categoriesData) && categoriesData.length > 1 && (
              <>
                <Text
                  as="span"
                  fontSize="sm"
                  fontWeight="bold"
                  w={["100%", "auto"]}
                >
                  Filter by:
                </Text>
                {categoriesData
                  .filter(({ parent, count }) => parent === 0 && count !== 0)
                  .map((category) => {
                    return (
                      <Checkbox
                        key={category.id}
                        colorScheme={settings.theme_color_scheme || "blue"}
                        size="md"
                        borderColor="gray.100"
                        _hover={{
                          ".chakra-checkbox__input:not(:checked) + .chakra-checkbox__control":
                            {
                              bg: `${settings.theme_color_scheme}.200`,
                            },
                        }}
                        name={category.slug}
                        value={category.id}
                        onChange={handleFilter}
                      >
                        {category.name}
                      </Checkbox>
                    );
                  })}
              </>
            )}
          </Stack>

          <Box
            className="resources-wp-archive__search"
            as="form"
            my={2}
            onSubmit={handleSearch}
          >
            <Flex>
              <Input
                type="text"
                name="search"
                placeholder="Keywords..."
                size="sm"
                my="0 !important"
                borderRadius={3}
                borderTopRightRadius={0}
                borderBottomRightRadius={0}
              />
              <Button
                type="submit"
                size="sm"
                colorScheme={settings.theme_color_scheme}
                color={colorSchemeMap(settings.theme_color_scheme)}
                ml="-1px"
                px={6}
                borderRadius={3}
                borderTopLeftRadius={0}
                borderBottomLeftRadius={0}
              >
                Search
              </Button>
            </Flex>
          </Box>
        </Flex>

        {!loading && data && isEmpty(data.resources) && (
          <Message
            className="resources-wp-archive__empty"
            message="Sorry, we couldn't find any results. Please try another query."
          />
        )}

        <Grid
          className="resources-wp-archive__list"
          templateColumns="repeat(auto-fill, minmax(272px, 1fr))"
          gap="20px"
          mb={10}
          ref={topRef}
        >
          {loading &&
            [...Array(8).keys()].map((_, index) => (
              <SkeletonCard key={index} />
            ))}

          {data &&
            !isEmpty(data.resources) &&
            data.resources.map((resource) => {
              const { created_at, updated_at } = resource;
              const dateStatus = getDateStatus(created_at, updated_at);
              const resourceParentCategories = resource.categories.filter(
                ({ parent }) => parent === 0
              );

              return (
                <Card
                  className="resources-wp-archive__list-item"
                  as={RouterLink}
                  to={`/${config.slug}/${resource.slug}/`}
                  image={resource.image}
                  title={resource.title}
                  description={resource.description}
                  status={dateStatus}
                  categories={resourceParentCategories}
                  key={resource.id}
                  cursor="pointer"
                  colorScheme={settings.theme_color_scheme}
                />
              );
            })}
        </Grid>

        <Pagination
          className="resources-wp-archive__pagination"
          pages={data?.page_count}
          currentPage={currentPage}
          onChange={(page) => handlePageChange(page)}
        />
      </Container>
    </Box>
  );
}

export default Archive;
