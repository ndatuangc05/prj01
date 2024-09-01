import React from "react";
import { isEmpty } from "lodash";
import {
  EmailShareButton,
  TwitterShareButton,
  LinkedinShareButton,
  FacebookShareButton,
  EmailIcon,
  TwitterIcon,
  LinkedinIcon,
  FacebookIcon,
} from "react-share";
import { Box, Flex, Stack, Text, Icon, Tooltip } from "@chakra-ui/react";

const SocialShareButtons = ({ url, buttons, ...props }) => {
  if (!url || isEmpty(buttons)) return null;

  return (
    <Stack align="center" direction="row" spacing={3} my={2} {...props}>
      <Text fontWeight="bold">Share: </Text>
      <Flex
        as="ul"
        listStyleType="none"
        flexDirection="row"
        flexWrap="wrap"
        alignItems="center"
      >
        {buttons.includes("email") && (
          <Box as="li" m={2}>
            <Tooltip label="Email">
              <EmailShareButton url={url}>
                <Icon as={EmailIcon} w={8} h={8} />
              </EmailShareButton>
            </Tooltip>
          </Box>
        )}

        {buttons.includes("twitter") && (
          <Box as="li" m={2}>
            <Tooltip label="Twitter">
              <TwitterShareButton url={url}>
                <Icon as={TwitterIcon} w={8} h={8} />
              </TwitterShareButton>
            </Tooltip>
          </Box>
        )}

        {buttons.includes("linkedin") && (
          <Box as="li" m={2}>
            <Tooltip label="LinkedIn">
              <LinkedinShareButton url={url}>
                <Icon as={LinkedinIcon} w={8} h={8} />
              </LinkedinShareButton>
            </Tooltip>
          </Box>
        )}

        {buttons.includes("facebook") && (
          <Box as="li" m={2}>
            <Tooltip label="Facebook">
              <FacebookShareButton url={url}>
                <Icon as={FacebookIcon} w={8} h={8} />
              </FacebookShareButton>
            </Tooltip>
          </Box>
        )}
      </Flex>
    </Stack>
  );
};

// const StyledSocialShareButtons = styled(SocialShareButtons)`
//   position: sticky;
//   top: 80px;
//   bottom: 80px;

//   .social-share-buttons__list {
//     list-style: none;
//     position: absolute;
//     top: 30px;
//     right: 0;
//     margin: 0;
//     padding: 0;

//     li {
//       margin-bottom: 15px;
//     }

//     [class*="fa-"] {
//       color: ${({ theme }) => theme.colors.green};
//       font-size: 20px;
//     }

//     button {
//       width: 50px;
//       height: 50px;
//       border: 1px solid ${({ theme }) => theme.colors.green} !important;
//       border-radius: 50% !important;
//       background-color: white !important;
//       transition: background 300ms ease;

//       &:hover {
//         background-color: ${({ theme }) => theme.colors.green} !important;

//         [class*="fa-"] {
//           color: ${({ theme }) => theme.colors.light};
//         }
//       }
//     }
//   }

//   @media screen and (max-width: 1030px) {
//     position: relative;
//     top: 0;
//     text-align: center;

//     .social-share-buttons__list {
//       position: relative;
//       top: 0;
//       margin: 1rem 0;
//       padding: 0;

//       li {
//         display: inline-block;
//         margin-bottom: 0;
//         margin-right: 15px;
//       }
//     }
//   }
// `

export default SocialShareButtons;
