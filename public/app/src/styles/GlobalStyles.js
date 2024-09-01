import { Global, css } from "@emotion/react";

export const GlobalStyles = () => (
  <Global
    styles={css`
      #resources-wp {
        .hs-form {
          .hs-form-field {
            position: relative;

            > label {
              color: rgb(26, 32, 44);
              font-size: 14px;
              font-weight: 300;
              line-height: 1;
            }
          }

          input {
            color: rgb(26, 32, 44);
            font-size: 18px;
            line-height: 1;
            text-align: left;
            width: 100%;
            height: auto;
            margin-top: 5px;
            /* padding: 10px 30px; */
            background: none;
            border-bottom: 1px solid rgba(26, 32, 44, 0.1);
            outline: none;

            &:focus {
              border-bottom-color: rgba(26, 32, 44, 0.5);
            }

            &::placeholder {
              font-weight: 300;
            }

            &[type="submit"] {
              color: rgb(26, 32, 44);
              font-size: 18px;
              font-weight: 300;
              text-align: center;
              display: inline-block;
              width: auto;
              min-width: 200px;
              height: 50px;
              margin: 60px 0 0;
              padding: 12px 25px;
              border: none;
              border-radius: 6px;
              background-color: rgb(230, 236, 243);
              cursor: pointer;

              &:focus,
              &:hover {
                background-color: rgb(230, 236, 243);
              }
            }
          }

          select {
            font-size: 18px;
            line-height: 1;
            text-align: center;
            width: 100%;
            margin-top: 5px;
            /* padding: 10px 30px; */
            background-color: transparent;
            border-bottom: 1px solid rgba(26, 32, 44, 0.1);

            &.is-placeholder {
              color: rgba(26, 32, 44, 0.7);
              font-weight: 300;
            }
          }

          .hs-error-msgs {
            list-style: none;
            padding: 5px 10px 10px 10px;
          }

          .hs-error-msgs .hs-error-msg {
            color: rgb(26, 32, 44);
            font-size: 14px;
          }

          .submitted-message {
            font-size: 14px;
          }
        }
      }
    `}
  />
);

export default GlobalStyles;
