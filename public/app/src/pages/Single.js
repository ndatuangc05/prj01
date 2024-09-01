import React, { useEffect } from "react";
import { Link as RouterLink } from "react-router-dom";
import parse from "html-react-parser";
import {
  Box,
  Container,
  Heading,
  Image,
  Badge,
  SimpleGrid,
  Stack,
  Link,
} from "@chakra-ui/react";
import { ChevronLeftIcon } from "@chakra-ui/icons";
import { getDateStatus } from "../utils/helpers";
import useFetch from "../hooks/useFetch";
import Message from "../components/Message";
import Hero from "../components/Hero";
import Assets from "../components/Assets";
import SocialShareButtons from "../components/SocialShareButtons";

function Single({ config, settings, match }) {
  const slug = match.params.slug;

  const url = `${config.apiUrl}/resource/${slug}/`;

  const [{ error, data }] = useFetch(url);

  useEffect(() => {
    window.scrollTo({
      top: 0,
      left: 0,
      behavior: "smooth",
    });
  }, []);

  if (error) return <Message message="Error... please refresh your browser." />;

  return (
    <Box className="resources-wp-single">
      {settings && settings.single_header_enabled && (
        <Hero
          className="resources-wp-single__hero"
          kicker={settings.single_header_kicker}
          title={settings.single_header_title}
          content={settings.single_header_content}
          color={settings.single_header_font_color}
          bgColor={settings.single_header_background_color}
          bgImage={settings.single_header_background_image}
        />
      )}
      <Container
        className="resources-wp-single__container"
        maxW="900px"
        py={20}
      >
        <Stack
          className="resources-wp-single__breadcrumbs"
          spacing={10}
          direction="row"
          mb={3}
        >
          <Link
            as={RouterLink}
            to={`/${config.slug}/`}
            color="gray.600"
            fontSize="xs"
            fontWeight="semibold"
            letterSpacing="wide"
            textTransform="uppercase"
            display="inline-flex"
            alignItems="center"
            mb={3}
          >
            <ChevronLeftIcon w={4} h={4} mr={1} /> All Resources
          </Link>
        </Stack>
        {data && (
          <Box>
            <SimpleGrid
              className="resources-wp-single__header"
              minChildWidth={["100%", "430px"]}
              spacing="40px"
              mb={10}
            >
              {data.image && data.image.url && (
                <Box
                  className="resources-wp-single__image"
                  h="280px"
                  bgColor="gray.50"
                  bgRepeat="no-repeat"
                  bgPosition="center"
                  borderRadius={3}
                  style={{
                    backgroundImage: `url('${data.image.url}')`,
                    backgroundSize: data.image.background_size,
                  }}
                >
                  <Image
                    src={data.image.url}
                    alt={data.image.alt}
                    display="none"
                  />
                </Box>
              )}

              <Box>
                <Box
                  className="resources-wp-single__meta"
                  display="flex"
                  alignItems="center"
                  mb={2}
                >
                  {data.created_at && data.updated_at && (
                    <Badge
                      className="resources-wp-single__status"
                      colorScheme={settings.theme_color_scheme}
                      mr={6}
                      mb={3}
                      px={4}
                      py={1}
                      borderRadius={3}
                    >
                      {getDateStatus(data.created_at, data.updated_at)}
                    </Badge>
                  )}

                  {data.categories && (
                    <Box
                      className="resources-wp-single__categories"
                      color="gray.600"
                      fontWeight="semibold"
                      letterSpacing="wide"
                      fontSize="xs"
                      textTransform="uppercase"
                      mb={3}
                    >
                      {data.categories.map((category, index) =>
                        parse((index ? " &bull; " : "") + category.name)
                      )}
                    </Box>
                  )}
                </Box>

                {data.title && (
                  <Heading
                    className="resources-wp-single__title"
                    as="h1"
                    color="gray.700"
                    mb={2}
                  >
                    {data.title}
                  </Heading>
                )}

                <SocialShareButtons
                  className="resources-wp-single__social-share"
                  url={window.location.href}
                  buttons={data.social_share_buttons}
                  mt={6}
                  mb={0}
                />
              </Box>
            </SimpleGrid>

            <Box className="resources-wp-single__body">
              <Assets
                className="resources-wp-single__assets"
                colorScheme={settings.theme_color_scheme}
                assets={data.assets}
              />
            </Box>
          </Box>
        )}
      </Container>
    </Box>
  );
}

export default Single;
