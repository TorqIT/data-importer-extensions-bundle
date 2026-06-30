import React from "react";
import { Input } from "antd";
import { Form, FormKit } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeResolverRenderProps } from "../../../common/types/DynamicTypeResolverRegistry";

export function AdvancedPathLoadResolverSettings(_props: DynamicTypeResolverRenderProps): React.JSX.Element {
    return (
        <FormKit.Panel>
            <Form.Item
                name={["resolverConfig", "loadingStrategy", "settings", "advancedPath"]}
                label="Advanced Path"
                required
                rules={[{ required: true, message: "Advanced path is required." }]}
                extra="Dynamic path expression built from input data"
            >
                <Input />
            </Form.Item>
        </FormKit.Panel>
    );
}
