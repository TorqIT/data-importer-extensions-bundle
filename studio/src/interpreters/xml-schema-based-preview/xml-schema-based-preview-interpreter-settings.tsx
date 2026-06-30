import React from "react";
import { Input } from "antd";
import { Form, FormKit } from "@pimcore/studio-ui-bundle/components";

export function XmlSchemaBasedPreviewInterpreterSettings(): React.JSX.Element {
    return (
        <FormKit.Panel>
            <Form.Item name={["interpreterConfig", "settings", "xpath"]} label="XPath">
                <Input.TextArea autoSize={{ minRows: 2, maxRows: 6 }} />
            </Form.Item>
            <Form.Item name={["interpreterConfig", "settings", "schema"]} label="Schema">
                <Input.TextArea autoSize={{ minRows: 4, maxRows: 12 }} />
            </Form.Item>
        </FormKit.Panel>
    );
}
