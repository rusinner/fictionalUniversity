import "./index.scss";
import {
  TextControl,
  Flex,
  FlexBlock,
  FlexItem,
  Icon,
  Button,
} from "@wordpress/components";

wp.blocks.registerBlockType("ourplugin/are-you-paying-attention", {
  title: "Are You Paying Attention?",
  icon: "smiley",
  category: "common",
  attributes: {
    skyColor: {
      type: "string",
      // source: "text",
      // selector: ".skyColor",
    },
    grassColor: {
      type: "string",
      // source: "text",
      // selector: ".grassColor",
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
  function updateSkyColor(e) {
    props.setAttributes({ skyColor: e.target.value });
  }
  function updateGrassColor(e) {
    props.setAttributes({ grassColor: e.target.value });
  }
  return (
    <div className="paying-attention-edit-block">
      <TextControl label="Question:" style={{ fontSize: "20px" }} />
      <p style={{ fontSize: "12px", margin: "2-px 0 8px 0" }}>Answers:</p>
      <Flex>
        <FlexBlock>
          <TextControl />
        </FlexBlock>
        <FlexItem>
          <Button>
            <Icon icon="star-empty" className="mark-as-correct" />
          </Button>
        </FlexItem>
        <FlexItem>
          <Button isLink className="attention-delete">
            Delete
          </Button>
        </FlexItem>
      </Flex>
      <Button isPrimary>Add Another Answer</Button>
    </div>
  );
}
