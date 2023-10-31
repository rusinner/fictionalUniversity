import "./index.scss";
import {
  TextControl,
  Flex,
  FlexBlock,
  FlexItem,
  Icon,
  Button,
} from "@wordpress/components";

//this function is being called anytime anything changes.
//Checks if there is a any unanswered question and  enables update button only there isn't
(function () {
  let locked = false;
  wp.data.subscribe(function () {
    const results = wp.data
      .select("core/block-editor")
      .getBlocks()
      .filter(function (block) {
        return (
          block.name == "ourplugin/are-you-paying-attention" &&
          block.attributes.correctAnswer == undefined
        );
      });
    if (results.length && locked == false) {
      locked = true;
      wp.data.dispatch("core/editor").lockPostSaving("noanswer");
    }
    if (!results.length && locked) {
      locked = false;
      wp.data.dispatch("core/editor").unlockPostSaving("noanswer");
    }
  });
})();

wp.blocks.registerBlockType("ourplugin/are-you-paying-attention", {
  title: "Are You Paying Attention?",
  icon: "smiley",
  category: "common",
  attributes: {
    question: {
      type: "string",
      // source: "text",
      // selector: ".skyColor",
    },
    answers: {
      type: "array",
      default: [""],
    },
    correctAnswer: {
      type: "Number",
      default: undefined,
    },
  },
  //edit function defines what appears on edit tool
  edit: EditComponent,
  //save function defines what appears on actual block
  save: function (props) {
    return null;
  },
});

function EditComponent(props) {
  // updates question in real time on click
  function updateQuestion(value) {
    props.setAttributes({ question: value });
  }

  function deleteAnswer(indexToDelete) {
    const newAnswers = props.attributes.answers.filter((x, index) => {
      return index !== indexToDelete;
    });
    props.setAttributes({ answers: newAnswers });
    if (indexToDelete == props.attributes.correctAnswer) {
      props.setAttributes({ correctAnswer: undefined });
    }
  }

  function markAsCorrect(index) {
    props.setAttributes({ correctAnswer: index });
  }

  return (
    <div className="paying-attention-edit-block">
      <TextControl
        label="Question:"
        value={props.attributes.question}
        onChange={() => updateQuestion}
        style={{ fontSize: "20px" }}
      />
      <p style={{ fontSize: "12px", margin: "2-px 0 8px 0" }}>Answers:</p>
      {props.attributes.answers.map((answer, index) => (
        <Flex>
          <FlexBlock>
            <TextControl
              value={answer}
              autoFocus={answer == undefined}
              onChange={(newValue) => {
                const newAnswers = props.attributes.answers.concat([]);
                newAnswers[index] = newValue;
                props.setAttributes({ answers: newAnswers });
              }}
            />
          </FlexBlock>
          <FlexItem>
            <Button>
              <Icon
                icon={
                  props.attributes.correctAnswer == index
                    ? "star-filled"
                    : "star-empty"
                }
                className="mark-as-correct"
                onClick={() => markAsCorrect(index)}
              />
            </Button>
          </FlexItem>
          <FlexItem>
            <Button
              onClick={() => deleteAnswer(index)}
              isLink
              className="attention-delete"
            >
              Delete
            </Button>
          </FlexItem>
        </Flex>
      ))}
      <Button
        onClick={() => {
          props.setAttributes({
            //concat new addition answer with value of undefined so i can call it above in TextControl component to focus on creation.
            answers: props.attributes.answers.concat([undefined]),
          });
        }}
        isPrimary
      >
        Add Another Answer
      </Button>
    </div>
  );
}
