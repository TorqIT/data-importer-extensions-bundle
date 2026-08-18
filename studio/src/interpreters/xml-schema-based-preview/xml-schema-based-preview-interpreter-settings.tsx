import React from "react";
import { Input } from "antd";
import { Form, FormKit } from "@pimcore/studio-ui-bundle/components";

export function XmlSchemaBasedPreviewInterpreterSettings(): React.JSX.Element {
    return (
      <FormKit.Panel>
        <Form.Item
          label="XPath"
          name={ ["interpreterConfig", "settings", "xpath"] }
        >
          <Input.TextArea autoSize={ { minRows: 2, maxRows: 6 } } />
        </Form.Item>
        <Form.Item
          label="Schema"
          name={ ["interpreterConfig", "settings", "schema"] }
        >
          <Input.TextArea autoSize={ { minRows: 4, maxRows: 12 } } />
        </Form.Item>
      </FormKit.Panel>
    );
}
