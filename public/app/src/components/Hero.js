import React from "react";
import { isEmpty } from "lodash";
import parse from "html-react-parser";
import { Box, Container, Heading, Text, Skeleton } from "@chakra-ui/react";

export function HeroSkeleton() {
  return <Skeleton height="580px" />;
}

function Hero({
  className,
  kicker,
  title,
  content,
  color,
  bgColor,
  bgImage,
  ...props
}) {
  return (
    <Box
      as="section"
      className={`resources-wp-hero ${className}`}
      position="relative"
      p="80px 30px"
      backgroundColor={bgColor ? bgColor : "gray.100"}
      backgroundImage={!isEmpty(bgImage) ? bgImage.url : null}
      bgRepeat="no-repeat"
      bgPosition="center"
      bgSize="cover"
      {...props}
    >
      <Container className="resources-wp-hero__container" maxW="1440px">
        <Box
          className="resources-wp-hero__row"
          display={["block", "block", "flex"]}
          alignItems="center"
        >
          <Box className="resources-wp-hero__col" flexBasis="50%" maxW="600px">
            {kicker && (
              <Text
                className="resources-wp-hero__kicker"
                color={color ? color : "#222222"}
                fontSize={["22px", "28px", "32px"]}
                fontWeight={300}
                m={0}
              >
                {kicker}
              </Text>
            )}

            {title && (
              <Heading
                as="h1"
                className="resources-wp-hero__title"
                color={color ? color : "#222222"}
                fontSize={["40px", "50px", "60px"]}
                fontWeight={700}
                m="0 0 1rem"
              >
                {title}
              </Heading>
            )}

            {content && (
              <Box
                className="resources-wp-hero__content"
                color={color ? color : "#222222"}
              >
                {parse(content)}
              </Box>
            )}
          </Box>
        </Box>
      </Container>
    </Box>
  );
}

export default Hero;
