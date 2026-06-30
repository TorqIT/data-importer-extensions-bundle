import React from "react";
import { Input } from "antd";
import { Form, FormKit } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeResolverRenderProps } from "../../../common/types/DynamicTypeResolverRegistry";

export function PropertyLoadResolverSettings(_props: DynamicTypeResolverRenderProps): React.JSX.Element {
    return (
        <FormKit.Panel>
            <Form.Item
                name={["resolverConfig", "loadingStrategy", "settings", "propertyName"]}
                label="Property Name"
                required
                rules={[{ required: true, message: "Property name is required." }]}
            >
                <Input />
            </Form.Item>
            <Form.Item
                name={["resolverConfig", "loadingStrategy", "settings", "valueIndex"]}
                label="Value Index"
                extra="Data source column containing the property value"
            >
                <Input />
            </Form.Item>
        </FormKit.Panel>
    );
}
