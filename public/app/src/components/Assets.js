import React, { useRef, useState } from "react";
// import ReactPlayer from "react-player";
import {
  Box,
  Flex,
  Heading,
  Image as ChakraImage,
  Button,
  List,
  ListItem,
  ListIcon,
  Modal,
  ModalOverlay,
  ModalContent,
  ModalBody,
  ModalCloseButton,
  useDisclosure,
} from "@chakra-ui/react";
import { ArrowForwardIcon } from "@chakra-ui/icons";
import ContentComponent from "./Content";

function Content({ colorScheme, content }) {
  return (
    <ContentComponent colorScheme={colorScheme} content={content} my={6} />
  );
}

function File({ file }) {
  const { url, filename, filesize, type } = file;

  return (
    <Box
      my={6}
      p={3}
      border="1px solid"
      borderColor="gray.100"
      borderRadius="6px"
    >
      <Box
        display={["block", "flex"]}
        flexWrap="wrap"
        justifyContent="space-between"
      >
        <Box flex={"1 1 70%"} mx={3} my={2}>
          <Heading as="h4" fontSize="md" fontWeight="bold" mb={3}>
            {filename}
          </Heading>
          <List fontSize="sm">
            <ListItem>
              <ListIcon as={ArrowForwardIcon} color="green.500" />
              Size: <strong>{filesize}</strong>
            </ListItem>
            <ListItem>
              <ListIcon as={ArrowForwardIcon} color="green.500" />
              Type: <strong>{type}</strong>
            </ListItem>
          </List>
        </Box>
        <Flex wrap="wrap" flex="1 1 calc(30% - 32px)" my={1}>
          <Button
            as="a"
            size="sm"
            fontSize="sm"
            flex="1 1 150px"
            maxW="100%"
            m={1}
            href={url}
            download
          >
            Download
          </Button>
        </Flex>
      </Box>
    </Box>
  );
}

function Image({ image }) {
  const { url, filename, filesize, type } = image;

  const { isOpen, onOpen, onClose } = useDisclosure();

  return (
    <Box
      my={6}
      p={3}
      border="1px solid"
      borderColor="gray.100"
      borderRadius="6px"
    >
      <Box
        display={["block", "flex"]}
        flexWrap="wrap"
        justifyContent="space-between"
      >
        <Box
          display={["block", "flex"]}
          flexDirection="row"
          flex="1 1 70%"
          mx={3}
          my={2}
        >
          <ChakraImage
            borderRadius="full"
            boxSize="80px"
            src={url}
            alt={filename}
            objectFit="cover"
            mr={[0, 4]}
            mb={[4, 0]}
          />
          <Box>
            <Heading as="h4" fontSize="md" fontWeight="bold" mb={3}>
              {filename}
            </Heading>
            <List fontSize="sm">
              <ListItem>
                <ListIcon as={ArrowForwardIcon} color="green.500" />
                Size: <strong>{filesize}</strong>
              </ListItem>
              <ListItem>
                <ListIcon as={ArrowForwardIcon} color="green.500" />
                Type: <strong>{type}</strong>
              </ListItem>
            </List>
          </Box>
        </Box>
        <Flex wrap="wrap" flex="1 1 calc(30% - 32px)" my={1}>
          <Button size="sm" flex="1 1 150px" m={1} onClick={onOpen}>
            Open
          </Button>
          <Button as="a" size="sm" flex="1 1 150px" m={1} href={url} download>
            Download
          </Button>
        </Flex>
      </Box>

      <Modal isOpen={isOpen} onClose={onClose} isCentered>
        <ModalOverlay />
        <ModalContent>
          <ModalCloseButton />
          <ModalBody p={20}>
            <ChakraImage src={url} alt={filename} objectFit="cover" />
          </ModalBody>
        </ModalContent>
      </Modal>
    </Box>
  );
}

function Video({ video }) {
  const { url, thumbnail } = video;

  if (!url) return null;

  return (
    <Box position="relative" maxW="100%" my={6} pt="56.25%">
      <Box
        as="iframe"
        src={url}
        position="absolute"
        top="0"
        left="0"
        width="100% !important"
        height="100% !important"
      />
    </Box>
  );
}

// function Video({ video }) {
//   const { url, thumbnail } = video;

//   const playerRef = useRef(null);

//   const [state, setState] = useState({
//     playing: false,
//     loop: false,
//     volume: 1,
//     muted: false,
//   });

//   // useEffect(() => {
//   //   if (playerRef.current) {
//   //     console.log(playerRef.current);
//   //     playerRef.current.handleClickPreview((e) => {
//   //       console.log(e);
//   //       // setState((prevState) => ({ ...prevState, playing: true }))
//   //     });
//   //   }
//   // }, []);

//   if (!url) return null;

//   return (
//     <Box position="relative" maxW="100%" my={6} pt="56.25%">
//       <Box
//         as={ReactPlayer}
//         ref={playerRef}
//         url={url}
//         playing={state.playing}
//         loop={state.loop}
//         volume={state.volume}
//         muted={state.muted}
//         light={thumbnail.url || true}
//         playsinline={true}
//         // playIcon={<span />}
//         position="absolute"
//         top="0"
//         left="0"
//         width="100% !important"
//         height="100% !important"
//         onClick={() => setState({ ...state, playing: !state.playing })}
//       />
//     </Box>
//   );
// }

function AssetController(props) {
  const { _type, colorScheme } = props;

  if (_type === "content") {
    return <Content colorScheme={colorScheme} {...props} />;
  }

  if (_type === "file") {
    return <File {...props} />;
  }

  if (_type === "image") {
    return <Image {...props} />;
  }

  if (_type === "video") {
    return <Video {...props} />;
  }

  return null;
}

function Assets({ colorScheme, assets }) {
  return (
    <div>
      {assets.map((asset, index) => {
        return (
          <AssetController colorScheme={colorScheme} {...asset} key={index} />
        );
      })}
    </div>
  );
}

export default Assets;
