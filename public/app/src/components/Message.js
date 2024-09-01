import React from "react";
import { Center, Text } from "@chakra-ui/react";

function Message({ message }) {
  if (!message) return null;

  return (
    <Center
      justifyContent="center"
      alignItems="center"
      minH="80vh"
      py={30}
      bgColor="white"
    >
      <Text fontSize="lg" fontWeight={300}>
        {message}
      </Text>
    </Center>
  );
}

export default Message;
