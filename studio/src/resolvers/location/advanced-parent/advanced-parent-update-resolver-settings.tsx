import React from "react";
import { Input } from "antd";
import { Form, FormKit } from "@pimcore/studio-ui-bundle/components";
import { type DynamicTypeResolverRenderProps } from "../../../common/types/DynamicTypeResolverRegistry";

export function AdvancedParentUpdateResolverSettings(_props: DynamicTypeResolverRenderProps): React.JSX.Element {
    return (
        <FormKit.Panel>
            <Form.Item
                name={["resolverConfig", "locationUpdateStrategy", "settings", "advancedParent"]}
                label="Advanced Parent Path"
                required
                rules={[{ required: true, message: "Advanced parent path is required." }]}
                extra="Dynamic path expression built from input data"
            >
                <Input />
            </Form.Item>
            <Form.Item
                name={["resolverConfig", "locationUpdateStrategy", "settings", "fallbackPath"]}
                label="Fallback Path"
                extra="Used if the computed parent path cannot be resolved"
            >
                <Input />
            </Form.Item>
        </FormKit.Panel>
    );
}
